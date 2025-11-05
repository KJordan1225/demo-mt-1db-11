<?php

namespace App\Http\Controllers\Tenant;


use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\DB;

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
        
        $pendingRequests = 0;
        $newSignups = 0;
        $activeSubscriptions = 0;
        $userCount = 0;
        $subscriptions = [];
        $notifications = [];

        $firstSegment = request()->segment(1);
        tenancy()->initialize($firstSegment); 
        // if (tenancy()->initialized) {
        //     dump([
        //         'initialized' => tenancy()->initialized,
        //         'tenant_id'   => tenant('id'),   // null if not initialized
        //     ]);
        // }
        $userId = $request->session()->get('user_id');
        // $user = User::find($userId); 
        $user = User::withoutGlobalScopes()->find($userId);
        
        return view('tenant.admin.dashboard', 
            compact('pendingRequests', 
                    'newSignups', 
                    'activeSubscriptions',
                    'userCount',
                    'subscriptions',
                    'notifications',
                    'user'));
    }
}   

