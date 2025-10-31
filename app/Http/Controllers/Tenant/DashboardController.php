<?php

namespace App\Http\Controllers\Tenant;

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

    public function index2(Request $request)
    {
       return view('dashboard');
    }

    public function tenantAdminDashboard()
    {
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
                    'notifications'));
    }
}   

