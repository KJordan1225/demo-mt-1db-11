<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Stancl\Tenancy\Tenancy;  // Import the Auth facade
use App\Models\User;
use Illuminate\Validation\ValidationException;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // 1) Ensure the guard you expect
        Auth::shouldUse('tenant'); // or 'web' if you use a single guard

        // 2) Tenancy should already be initialized by middleware
        $tenantId = tenant('id') ?? $request->route('tenant'); // safe fallback

        // 3) Scope credentials to the tenant if you're on single-DB multi-tenancy
        $credentials = $request->only('email', 'password');
        if ($tenantId) {
            $credentials['tenant_id'] = $tenantId; // IMPORTANT for single-db setups
        }

        // 4) Attempt login on the chosen guard
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
        
        // Detect (or gently initialize) tenant context
        /** @var Tenancy $tenancy */
        $tenancy  = app(Tenancy::class);
        $isTenant = tenancy()->initialized ?? false;

        // Extract first segment from url
        // Get the full URL path
            $urlPath = parse_url(request()->getRequestUri(), PHP_URL_PATH);
            // Remove the leading slash and split the URL into segments
            $segments = explode('/', trim($urlPath, '/'));
            // Get the first segment
            $firstSegment = $segments[0] ?? null; 


        if (! $isTenant) {
            // Optional: try to initialize from route param if present
            if ($tenantParam = request()->query('tenant')) {
                $tenantModel = \App\Models\Tenant::query()
                    ->where('id', $tenantParam)
                    ->first();
                if ($tenantModel) {
                    $tenancy->initialize($tenantModel);
                    $isTenant = true;
                }
            }
        }

        $tenantId = $isTenant ? tenant('id') : null;
        // 1) Landlord-level super admin?
        if ($user->hasRole('super-admin', null)) {
            
           return redirect()->intended(route('landlord.dashboard.index')); 
            
        }
        // 2) Tenant admin?
        if ($isTenant && $user->hasRole('admin', $tenantId)) {
            
            return redirect()->intended(route('tenant.admin.dashboard', ['tenant' => $tenantId]));
            
        }

        // 3) Regular tenant user
        if ($isTenant) {
            
            return redirect()->intended(route('tenant.user-dashboard', ['tenant' => $tenantId]));
            
        }

        // 4) Fallback: central guest
        return redirect()->intended(route('guest.home', absolute: false));
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

}
