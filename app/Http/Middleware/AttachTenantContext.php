<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttachTenantContext
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set(
            'tenantId',
            (function_exists('tenant') && tenant())
                ? (method_exists(tenant(), 'getTenantKey') ? tenant()->getTenantKey() : tenant('id'))
                : null // landlord
        );
        return $next($request);
    }
}
