<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

// Laravel built‑ins
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

// Spatie middleware
use Spatie\Permission\Middlewares\RoleMiddleware;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;

class Kernel extends HttpKernel
{
    /** Route middleware aliases */
    protected $middlewareAliases = [
        // Laravel
        'auth'       => Authenticate::class,
        'guest'      => RedirectIfAuthenticated::class,
        'verified'   => EnsureEmailIsVerified::class,
        'bindings'   => SubstituteBindings::class,
        'throttle'   => ThrottleRequests::class,
    ];
}
