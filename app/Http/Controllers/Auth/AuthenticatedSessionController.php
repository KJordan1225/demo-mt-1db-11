<?php

namespace App\Http\Controllers\Auth;

use App\Models\Tenant;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\Auth\LoginRequest;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('tenant-switch');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // boolean
        $inTenant = tenancy()->initialized;

        // current tenant model or null
        $currentTenant = tenant();        // same as tenancy()->tenant
        $tenantId = tenant('id');         // null if not in tenant context

        if ($inTenant) {
            // tenant-specific logic
            return redirect()->intended(route('guest.home', ['tenant' => tenant('id')], absolute: false));
        } else {
                // landlord-specific logic
            return redirect()->intended(route('guest.home', absolute: false));
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function tenantStore(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        
        $request->session()->regenerate();        
        
        return redirect()->intended(route('guest.home', ['tenant' => tenant('id')], absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();        

        $request->session()->invalidate();

        $request->session()->regenerateToken();        

        return redirect('/');
    }

    public function tenantDestroy(Request $request): View
    {
        Auth::guard('web')->logout(); 
        
        $tenantId = tenant('id'); // Get the tenant's slug from the tenant context

        $request->session()->invalidate();

        $request->session()->regenerateToken();        

        return view('tenant.landing', [
            'tenantId' => $tenantId, 
        ]);

    }

}
