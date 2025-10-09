<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Model;

class EnsureModelBelongsToTenant
{
    public function handle($request, Closure $next)
    {
        $tenant = tenant('id');
        foreach ($request->route()->parameters() as $param) {
            if ($param instanceof Model && $param->getAttribute('tenant_id') !== $tenant) {
                abort(404);
            }
        }
        return $next($request);
    }
}
