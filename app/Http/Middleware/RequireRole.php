<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string $slug, string $scope='tenant')
    {
        $tenantId = $scope === 'tenant' ? tenant('id') : null;
        if (! $request->user() || ! $request->user()->hasRole($slug, $scope, $tenantId)) {
            abort(403);
        }
        return $next($request);
    }
}
