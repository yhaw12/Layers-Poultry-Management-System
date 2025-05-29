<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Only guests can access login/register, all others must be authenticated
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log login activity
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'description' => 'User logged in',
            ]);

            // Flash message
            session()->flash('status', Auth::user()->is_admin ? 'Logged in as Admin.' : 'Login successful.');

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        // Log logout activity
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'logout',
            'description' => 'User logged out',
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // $isAdmin = strtolower(trim($data['name'])) === 'admin';
        $isAdmin = $data['email'] === env('ADMIN_EMAIL', 'admin@example.com');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => $isAdmin,
        ]);

        Auth::login($user);

        // Log registration activity
        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            // 'description' => 'User registered',
           'description' => 'User registered' . ($isAdmin ? ' as admin' : ''),
        ]);

        return redirect()->route('dashboard');
    }
}
