<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantSwitchController extends Controller
{
    public function index()
    {
        // fetch a simple list of tenants (id + optional name)
        $tenants = Tenant::query()
            ->select(['id', 'data']) // 'data' may include a display name
            ->orderBy('id')
            ->get();

        return view('tenant-switch', compact('tenants'));
    }

    public function switch(Request $request)
    {
        $validated = $request->validate([
            'tenant' => ['required', 'string'],
        ]);

        $tenantId = $validated['tenant'];

        // Ensure the tenant exists (in central DB)
        $exists = Tenant::query()->where('id', $tenantId)->exists();
        if (! $exists) {
            return back()
                ->withErrors(['tenant' => 'Tenant not found.'])
                ->withInput();
        }

        // Redirect to that tenant's login (or wherever you prefer)
        return redirect()->route('tenant.login', ['tenant' => $tenantId]);
    }
}
