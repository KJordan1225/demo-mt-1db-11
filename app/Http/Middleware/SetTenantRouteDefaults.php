<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;

class SetTenantRouteDefaults
{
    public function handle($request, Closure $next)
    {
        $tenant = tenant('id') ?? $request->route('tenant');
        if ($tenant) {
            URL::defaults(['tenant' => $tenant]); // route('login') -> /{tenant}/login
            view()->share('tenant', $tenant);
        }
        return $next($request);
    }
}
