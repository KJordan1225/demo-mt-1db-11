<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // $branding is already shared into views by the provider
        return view('tenant.dashboard', [
            'tenant' => tenant(), // sometimes handy in the view
        ]);
    }
}

