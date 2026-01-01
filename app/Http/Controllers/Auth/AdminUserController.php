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
use Spatie\Permission\PermissionRegistrar;

class AdminUserController extends Controller
{
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

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

            $roles = $data['roles'] ?? ($data['role'] ? [$data['role']] : []);
            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed creating user', ['err' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Failed to create user.');
        }
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::all()->groupBy(fn($perm) => Str::before($perm->name, '_') ?: 'general');

        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $data = $request->validate([
                'permissions'   => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
                'roles'         => 'nullable|array',
                'roles.*'       => 'string|exists:roles,name',
                'sync_roles'    => 'nullable|boolean',
                'role'          => 'nullable|string|exists:roles,name',
                'assign_role'   => 'nullable|boolean',
            ]);
        } catch (ValidationException $ve) {
            return response()->json(['success' => false, 'message' => 'Invalid input', 'errors' => $ve->errors()], 422);
        }

        DB::beginTransaction();
        try {
            if (isset($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            $roles = $data['roles'] ?? null;
            $syncRolesFlag = isset($data['sync_roles']) ? (bool)$data['sync_roles'] : null;

            if ($roles !== null) {
                if ($syncRolesFlag) {
                    $user->syncRoles($roles);
                } else {
                    foreach ($roles as $r) {
                        if (!$user->hasRole($r)) $user->assignRole($r);
                    }
                }
            } elseif (isset($data['assign_role']) && isset($data['role'])) {
                $data['assign_role'] ? $user->assignRole($data['role']) : $user->removeRole($data['role']);
            }

            DB::commit();

            // FIX: Clear Spatie cache and refresh the user model
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $user->refresh();

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action'  => 'updated_user_permissions_roles',
                'details' => "Updated user {$user->id} roles/permissions"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permissions/roles updated.',
                'user_roles' => $user->getRoleNames()->toArray(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Update failed', ['err' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update.'], 500);
        }
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['error' => 'You cannot delete yourself.'], 422);
        }

        try {
            if (!empty($user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete failed.'], 500);
        }
    }

    public function permissions(User $user)
    {
        try {
            $perms = Permission::orderBy('name')->get();
            $grouped = $perms->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'label' => ucwords(str_replace('_', ' ', $p->name))])
                ->groupBy(fn($perm) => Str::before($perm['name'], '_') ?: 'general')->toArray();

            $roles = Role::with('permissions')->orderBy('name')->get()->map(fn($r) => [
                'name' => $r->name,
                'display' => ucfirst($r->name),
                'permissions' => $r->permissions->pluck('name')->toArray(),
            ])->values()->toArray();

            return response()->json([
                'success' => true,
                'grouped' => $grouped,
                'roles' => $roles,
                'user_permissions' => $user->getPermissionNames()->toArray(),
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'user_roles' => $user->getRoleNames()->toArray(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Load failed.'], 500);
        }
    }

    public function togglePermission(Request $request, User $user)
    {
        $data = $request->validate(['permission' => 'required|string|exists:permissions,name']);
        try {
            $action = $user->hasPermissionTo($data['permission']) ? 'revoked' : 'granted';
            $action === 'revoked' ? $user->revokePermissionTo($data['permission']) : $user->givePermissionTo($data['permission']);
            return response()->json(['success' => true, 'action' => $action]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Toggle failed'], 500);
        }
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:4096']);
        $user = Auth::user();
        if ($user->avatar) Storage::disk('public')->delete('avatars/' . $user->avatar);
        $filename = 'user_' . $user->id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
        $path = $request->file('avatar')->storeAs('avatars', $filename, 'public');
        $user->update(['avatar' => $filename]);
        return response()->json(['success' => true, 'avatar_url' => Storage::disk('public')->url($path)]);
    }

    public function show(User $user)
    {
        return $this->permissions($user); // Reusing the permissions logic for modal display
    }
}