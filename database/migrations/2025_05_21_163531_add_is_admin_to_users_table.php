<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class IsAdmin
// {
//     public function handle(Request $request, Closure $next)
//     {
//           if (Auth::check() && Auth::user()->isAdmin) {
//             return $next($request); 
//         }

    
//         return redirect()->route('dashboard')->with('error', 'You do not have permission to perform this action.');
//     }
// }