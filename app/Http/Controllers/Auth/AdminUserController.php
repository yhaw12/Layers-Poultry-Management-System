<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    /**
     * Note: consider adding middleware or policy checks to protect these routes,
     * e.g. ->middleware('can:manage users') or using $this->authorize() where appropriate.
     */

    /**
     * Display a paginated listing of users with optional search/role filters.
     */
    public function index(Request $request)
    {
        $q = $request->input('q');
        $role = $request->input('role');
        $perPage = (int) $request->input('per_page', 10);

        $query = User::with('roles');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($role) {
            $query->whereHas('roles', fn($qr) => $qr->where('name', $role));
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->only(['q', 'role', 'per_page']));

        $roles = Role::orderBy('name')->get();
        $permissions = Permission::all()->groupBy(fn($perm) => Str::before($perm->name, '_') ?: 'general');

        return view('admin.users.index', compact('users', 'roles', 'permissions'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // accept either singular role (legacy) or roles[] (preferred)
            'role'     => ['nullable', 'string', 'exists:roles,name'],
            'roles'    => ['nullable', 'array'],
            'roles.*'  => ['string', 'exists:roles,name'],
        ]);

        try {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // prefer explicit roles[] if provided, otherwise fallback to role
            $roles = $data['roles'] ?? ($data['role'] ? [$data['role']] : []);
            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed creating user', ['err' => $e->getMessage(), 'input' => $request->all()]);
            return back()->withInput()->with('error', 'Failed to create user.');
        }
    }

    /**
     * Show edit form for a user.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::all()->groupBy(fn($perm) => Str::before($perm->name, '_') ?: 'general');

        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update user details (roles & permissions).
     *
     * Behavior:
     * - 'permissions' => full list to sync (syncPermissions)
     * - 'roles' => array of role names
     * - 'sync_roles' => boolean: if true, replace roles (syncRoles); if false or absent, additive assignment
     * - legacy 'assign_role' and 'role' are still supported for compatibility.
     */
    public function update(Request $request, User $user)
    {
        try {
            $data = $request->validate([
                'permissions'   => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
                'roles'         => 'nullable|array',
                'roles.*'       => 'string|exists:roles,name',
                'sync_roles'    => 'nullable|boolean',
                'role'          => 'nullable|string|exists:roles,name', // legacy
                'assign_role'   => 'nullable|boolean', // legacy
            ]);
        } catch (ValidationException $ve) {
            return response()->json(['success' => false, 'message' => 'Invalid input', 'errors' => $ve->errors()], 422);
        }

        $perms = $data['permissions'] ?? null;
        $roles = $data['roles'] ?? null;
        $syncRolesFlag = isset($data['sync_roles']) ? (bool)$data['sync_roles'] : null;
        $assignRoleFlag = isset($data['assign_role']) ? (bool)$data['assign_role'] : null;

        DB::beginTransaction();
        try {
            // sync permissions (if provided)
            if ($perms !== null) {
                $user->syncPermissions($perms);
            }

            // roles logic
            if (!empty($roles)) {
                if ($syncRolesFlag) {
                    // deterministic replace
                    $user->syncRoles($roles);
                } else {
                    // additive assignment: only add roles not already present
                    foreach ($roles as $r) {
                        if (!$user->hasRole($r)) $user->assignRole($r);
                    }
                }
            } elseif ($assignRoleFlag !== null && isset($data['role'])) {
                // legacy handling: single role + assign_role boolean
                if ($assignRoleFlag) {
                    $user->assignRole($data['role']);
                } else {
                    if ($user->hasRole($data['role'])) $user->removeRole($data['role']);
                }
            }

            DB::commit();

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action'  => 'updated_user_permissions_roles',
                'details' => "Updated user {$user->id} roles/permissions via admin panel"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permissions/roles updated.',
                'user_permissions' => $user->getPermissionNames()->toArray(),
                'user_roles' => $user->getRoleNames()->toArray(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('AdminUserController@update failed', ['err' => $e->getMessage(), 'user_id' => $user->id, 'input' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Failed to update permissions/roles.'], 500);
        }
    }

    /**
     * Delete a user (protects from deleting current user).
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'You cannot delete yourself.'], 422);
            }
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        try {
            // Optionally remove avatar file
            if (!empty($user->avatar) && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                try {
                    Storage::disk('public')->delete('avatars/' . $user->avatar);
                } catch (\Throwable $ex) {
                    Log::warning('Failed deleting avatar file for user: ' . $user->id, ['err' => $ex->getMessage()]);
                }
            }

            $user->delete();

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action'  => 'deleted_user',
                'details' => "Deleted user {$user->id} ({$user->email})",
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'User deleted.']);
            }

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed deleting user', ['error' => $e->getMessage()]);
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to delete user.'], 500);
            }
            return redirect()->route('users.index')->with('error', 'Failed to delete user.');
        }
    }

    /**
     * Toggle a single permission (AJAX-friendly).
     */
    public function togglePermission(Request $request, User $user)
    {
        $data = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        try {
            if ($user->hasPermissionTo($data['permission'])) {
                $user->revokePermissionTo($data['permission']);
                $action = 'revoked';
            } else {
                $user->givePermissionTo($data['permission']);
                $action = 'granted';
            }

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action'  => 'toggled_permission',
                'details' => "{$action} permission {$data['permission']} for user {$user->id}",
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'action' => $action]);
            }

            return redirect()->back()->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed toggling permission', ['error' => $e->getMessage()]);
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to update permission'], 500);
            }
            return redirect()->back()->with('error', 'Failed to update permission.');
        }
    }

    /**
     * Return grouped permissions and the user's current permissions (JSON).
     */
    public function permissions(User $user)
    {
        try {
            // fetch permissions and group them by prefix before the first underscore
            $perms = Permission::orderBy('name')->get();

            $grouped = $perms
                ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'label' => ucwords(str_replace('_', ' ', $p->name))])
                ->groupBy(fn($perm) => Str::before($perm['name'], '_') ?: 'general')
                ->toArray();

            // include roles (name + display + permission names) so frontend "Apply role defaults" works
            $roles = Role::with('permissions')->orderBy('name')->get()->map(function ($r) {
                return [
                    'name' => $r->name,
                    'display' => ucfirst($r->name),
                    'permissions' => $r->permissions->pluck('name')->toArray(),
                ];
            })->values()->toArray();

            $userPerms = $user->getPermissionNames()->toArray();

            return response()->json([
                'success' => true,
                'grouped' => $grouped,
                'roles' => $roles,
                'user_permissions' => $userPerms,
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'user_roles' => $user->getRoleNames()->toArray(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to load permissions JSON', ['err' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'user' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load permissions.',
            ], 500);
        }
    }

    /**
     * Sync an array of permission names for a user (AJAX).
     */
    public function updatePermissions(Request $request, User $user)
    {
        $data = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $permissions = $data['permissions'] ?? [];

        try {
            $user->syncPermissions($permissions);

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action'  => 'updated_permissions',
                'details' => "Synced permissions for user {$user->id}: " . implode(',', $permissions),
            ]);

            return response()->json(['success' => true, 'message' => 'Permissions updated.']);
        } catch (\Exception $e) {
            Log::error('Failed updating permissions', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update permissions.'], 500);
        }
    }

    /**
     * AJAX avatar upload — used by profile page JS.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $user = Auth::user();

        // Delete old avatar
        if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $ext = $request->file('avatar')->getClientOriginalExtension();
        $filename = 'user_' . $user->id . '_' . time() . '.' . $ext;

        $path = $request->file('avatar')->storeAs('avatars', $filename, 'public');

        $user->avatar = $filename;
        $user->save();

        return response()->json([
            'success' => true,
            'avatar_url' => Storage::disk('public')->url($path),
            'message' => 'Avatar updated successfully!',
        ]);
    }

    /**
     * Show a user's grouped permissions (for modal).
     */
    public function show(User $user)
    {
        try {
            $perms = Permission::orderBy('name')->get();

            $grouped = $perms->groupBy(function ($p) {
                $parts = explode('_', $p->name, 2);
                return $parts[0] ?? 'general'; // <-- use prefix (before first underscore)
            })->map(function ($group) {
                return $group->map(function ($p) {
                    return ['name' => $p->name, 'label' => ucwords(str_replace('_', ' ', $p->name))];
                })->values()->toArray();
            })->toArray();

            // Roles with their permissions (permission names)
            $roles = Role::with('permissions')->orderBy('name')->get()->map(function ($r) {
                return [
                    'name' => $r->name,
                    'display' => ucfirst($r->name),
                    'permissions' => $r->permissions->pluck('name')->toArray(),
                ];
            })->values()->toArray();

            $userPermissions = $user->getPermissionNames()->toArray();

            return response()->json([
                'success' => true,
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'grouped' => $grouped,
                'roles' => $roles,
                'user_permissions' => $userPermissions,
                'user_roles' => $user->getRoleNames()->toArray(),
            ]);
        } catch (\Throwable $e) {
            Log::error('UserPermissionsController@show failed', ['err' => $e->getMessage(), 'user' => $user->id]);
            return response()->json(['success' => false, 'message' => 'Failed to load permissions.'], 500);
        }
    }
}
