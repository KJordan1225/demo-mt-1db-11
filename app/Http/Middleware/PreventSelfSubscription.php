<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Resolvers\PathTenantResolver; // Not strictly needed for domain, but good practice

class PreventSelfSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Ensure a user is logged in
        if (!Auth::check()) {
            // Let the standard auth middleware handle this, or let the user hit the login page
           return $next($request);
        }

        // 2. Get the currently active tenant (the creator being subscribed to)
        $currentTenant = tenant(); 

        // 3. Get the logged-in user's associated central tenant (the user *is* a creator)
        // We use the currentTenant() method we defined on the User model.
        $loggedInCreatorTenant = Auth::user()->currentTenant();

        // dd($currentTenant, $loggedInCreatorTenant);

        // 4. Perform the comparison
        // Check if the current tenant's ID (the micro-site) matches the logged-in user's tenant ID
        if ($currentTenant && $loggedInCreatorTenant && $currentTenant->id === $loggedInCreatorTenant->id) {
            
            // The logged-in user is the owner of this micro-site. Block subscription.
            return redirect(route('tenant.landing', ['tenant' => $currentTenant->id]))
                ->with('error', 'You cannot subscribe to your own micro-site.');
        }

        return $next($request);
    }
}
