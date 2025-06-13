<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and is an admin
        if (Auth::check() && Auth::user()->isAdmin) {
            return $next($request); // Proceed to the route handler
        }

        // If not an admin, redirect with an error message
        return redirect()->route('dashboard')->with('error', 'You do not have permission to perform this action.');
    }
}