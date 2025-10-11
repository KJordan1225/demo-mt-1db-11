<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\SetTenantRouteDefaults;
use App\Providers\TenantBrandingServiceProvider;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // Initialize tenancy from a path parameter {tenant}
            'tenant'          => InitializeTenancyByPath::class,
            // Helper to auto-inject {tenant} into route() URLs while inside tenant pages
            'tenant.defaults' => SetTenantRouteDefaults::class,
            'universal'       => PreventAccessFromCentralDomains::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
        // register any core or package providers first (if you list any)
        // Illuminate\Filesystem\FilesystemServiceProvider::class,
        // …

        // ✅ your custom provider
        TenantBrandingServiceProvider::class,
        // or: App\Providers\TenantBrandingServiceProvider::class,
    ])
    ->create();
