<?php

namespace App\Http\Controllers\Connect;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stancl\Tenancy\Tenancy;          // ⬅️ add this
use Illuminate\Support\Facades\Gate;  // optional, if you want to authorize

class PricingController extends Controller
{
    public function store(Request $request, Tenant $tenant, Tenancy $tenancy)
    {
        // ---------------------------------------------------------
        // 1) Manually initialize tenancy if middleware didn't run
        // ---------------------------------------------------------
        if (!tenancy()->initialized) {
            // If you resolve tenants by slug in routes, $tenant is already a model;
            // just initialize the tenancy context with it.
            $tenancy->initialize($tenant);
        }

        if (! $tenant instanceof \App\Models\Tenant) {
            $tenant = \App\Models\Tenant::where('id', $tenant)->orWhere('slug', $tenant)->firstOrFail();
        }


        // (Optional) Authorization check (requires a Tenant policy)
        // Gate::authorize('update', $tenant);

        // ---------------------------------------------------------
        // 2) Validate request
        // ---------------------------------------------------------
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount'      => 'required|numeric|min:1', // USD dollars
        ]);
        
        // ---------------------------------------------------------
        // 3) Guard: tenant must be onboarded to Stripe
        // ---------------------------------------------------------
        abort_unless($tenant->stripe_account_id, 422, 'Creator not onboarded to Stripe.');

        // ---------------------------------------------------------
        // 4) Create Product & Price on the connected account
        // ---------------------------------------------------------
        Stripe::setApiKey(config('services.stripe.secret'));

        $product = Product::create([
            'name'        => $request->name ?: "Subscription for {$tenant->display_name}",
            'description' => $request->description,
            'metadata'    => ['tenant_id' => $tenant->id],
        ], [
            'stripe_account' => $tenant->stripe_account_id, // IMPORTANT: connected account
        ]);

        $price = Price::create([
            'unit_amount' => (int) round($request->amount * 100), // cents
            'currency'    => 'usd',
            'recurring'   => ['interval' => 'month'],
            'product'     => $product->id,
        ], [
            'stripe_account' => $tenant->stripe_account_id, // IMPORTANT: connected account
        ]);

        // ---------------------------------------------------------
        // 5) Persist to tenant row
        // ---------------------------------------------------------
        
        $tenant->forceFill([
            'stripe_account_id' => $tenant->stripe_account_id,
            'stripe_product_id' => $product->id,
            'stripe_price_id'   => $price->id,
        ])->save();
    
        
        // $tenant->stripe_account_id = $tenant->stripe_account_id;
        // $tenant->stripe_product_id = $product->id;
        // $tenant->stripe_price_id   = $price->id;
        // $tenant->save();

        // ---------------------------------------------------------
        // 6) Done
        // ---------------------------------------------------------
        return back()->with('status', 'Creator price created on connected account.');
    }
 
    public function showSubscriptionForm(Tenant $tenant, Tenancy $tenancy)
    {
        // ---------------------------------------------------------
        // 1) Manually initialize tenancy if middleware didn't run
        // ---------------------------------------------------------
        if (!tenancy()->initialized) {
            $tenancy->initialize($tenant);
        }

        // ---------------------------------------------------------
        // 2) Guard: tenant must have a price set up
        // ---------------------------------------------------------
        // abort_unless($tenant->stripe_price_id, 422, 'Creator has no price set up.');

        // ---------------------------------------------------------
        // 3) Show subscription form view
        // ---------------------------------------------------------
        return view('creator.admin.create_subscription_plan', ['tenant' => $tenant, 'priceId' => $tenant->stripe_price_id]);
    }   
}