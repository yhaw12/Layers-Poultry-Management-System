<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // Laravel's default middleware
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Spatie Permission middleware (optional, include if using spatie/laravel-permission)
        // 'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        // 'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        // 'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,

        // Your custom middleware
        'is_admin' => \App\Http\Middleware\CheckAdmin::class,
    ];

    // Other properties like $middlewareGroups, $middleware, etc., remain unchanged
}