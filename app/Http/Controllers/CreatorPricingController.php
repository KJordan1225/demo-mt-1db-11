<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\StripeService;
use Illuminate\Http\Request;

class CreatorPricingController extends Controller
{
    public function __construct(private StripeService $stripeService) {}

    public function update(Request $request)
    {
        $tenantId = $request->route('tenant');   // ← pull from route
        // Safe fallback: initialize tenancy if needed
        if (!tenancy()->initialized) { tenancy()->initialize($tenantId); }   // DIFFERENCE

        $tenant = tenant();
        $data = $request->validate([
            'plan_name'          => 'required|string|max:100',
            'plan_currency'      => 'nullable|string|size:3',
            'plan_amount_cents'  => 'required|integer|min:100', // $1.00 min
            'plan_interval'      => 'nullable|in:day,week,month,year',
        ]);

        $tenant->fill([
            'plan_name'         => $data['plan_name'],
            'plan_currency'     => strtolower($data['plan_currency'] ?? 'usd'),
            'plan_amount_cents' => (int) $data['plan_amount_cents'],
            'plan_interval'     => $data['plan_interval'] ?? 'month',
        ])->save(); // DIFFERENCE

        try {
            $priceId = $this->stripeService->ensureCreatorPrice($tenant); // DIFFERENCE
            return back()->with('success', "Pricing saved. Active price: {$priceId}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to sync price to Stripe: ' . $e->getMessage());
        }
    }
}

