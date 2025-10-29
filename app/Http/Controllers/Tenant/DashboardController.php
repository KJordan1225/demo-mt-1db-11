<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // $branding is already shared into views by the provider
        // return view('tenant.dashboard', [
        //     'tenant' => tenant(), // sometimes handy in the view
        // ]);

        return view('dashboard');
    }

    public function tenantAdminDashboard(Request $request)
    {
        
        // Extract first segment of url
        // Get the full URL path (excluding the domain)
        $urlPath = parse_url(request()->getRequestUri(), PHP_URL_PATH);
        // Remove leading and trailing slashes and split the path into segments
        $segments = explode('/', trim($urlPath, '/'));
        // Get the first segment
        $firstSegment = $segments[0] ?? null;  // Returns null if no segment exists

        $tenant = Tenant::where('id', $firstSegment)->first();
        
        $pendingRequests = 0;
        $newSignups = 0;
        $activeSubscriptions = 0;
        $userCount = 0;
        $subscriptions = [];
        $notifications = [];
        
        return view('tenant.admin.dashboard', 
            compact('pendingRequests', 
                    'newSignups', 
                    'activeSubscriptions',
                    'userCount',
                    'subscriptions',
                    'notifications',
                    'tenant',
                ));
    }
}   

