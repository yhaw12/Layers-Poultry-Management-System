<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Decide role (simple heuristic)
        $roleName = str_contains(strtolower($data['name']), 'admin') ||
                    str_contains(strtolower($data['email']), 'admin')
                    ? 'admin' : 'user';

        // Ensure role exists (guard_name explicit)
        $guardName = config('auth.defaults.guard', 'web') ?? 'web';
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guardName]);

        // Assign the role (safe: role exists now)
        $user->assignRole($role);

        Auth::login($user);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'details' => 'User registered',
        ]);

        return redirect()->route('dashboard');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'details' => 'User logged in',
            ]);

            session()->flash('status', $user->hasRole('admin') ? 'Logged in as Admin.' : 'Login successful.');

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'logout',
            'details' => 'User logged out',
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|string']);

        // ensure requested role exists
        $guardName = config('auth.defaults.guard', 'web') ?? 'web';
        $role = Role::firstOrCreate(['name' => $request->role, 'guard_name' => $guardName]);

        $user->assignRole($role);

        return redirect()->back()->with('success', 'Role assigned.');
    }
}
