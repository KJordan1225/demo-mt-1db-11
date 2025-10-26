<?php

namespace App\Http\Controllers\Connect;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\ConnectCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Tenancy;                 // ⬅️ for manual initialize()
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session as CheckoutSession;

class SubscriptionController extends Controller
{
    /**
     * Create a Checkout Session on the creator's CONNECTED account (mode=subscription).
     */
    public function subscribe(Request $request, Tenant $tenant, Tenancy $tenancy)
    {
        // ---------------------------------------------------------
        // 1) Manually initialize tenancy if it hasn't been initialized
        // ---------------------------------------------------------
        if (!tenancy()->initialized) {
            $tenancy->initialize($tenant);
        }

        // (optional) Gate/policy check:
        // $this->authorize('subscribeTo', $tenant);

        // ---------------------------------------------------------
        // 2) Preconditions
        // ---------------------------------------------------------
        abort_unless(
            $tenant->stripe_account_id && $tenant->stripe_price_id,
            422,
            'Creator not ready for subscriptions.'
        );

        $user = Auth::user();
        Stripe::setApiKey(config('services.stripe.secret'));
        $acctId = $tenant->stripe_account_id;
        
        // For a CONNECTED account (use the Connect header)
        \Stripe\Account::update(
            $tenant->stripe_account_id,
            [
                'business_profile' => [
                    'name' => $tenant->display_name ?: 'Creator Name',
                    'url'  => 'https://your-app.example/creator/' . $tenant->id, // recommended
                ],
            ],
            ['stripe_account' => $tenant->stripe_account_id] // <-- critical
        );


        // ---------------------------------------------------------
        // 3) Map platform user -> CONNECTED CUSTOMER on creator's account
        // ---------------------------------------------------------
        $map = ConnectCustomer::firstOrNew([
            'user_id'  => $user->id,
            'tenant_id'=> $tenant->id,
        ]);

        if (! $map->exists || empty($map->connected_customer_id)) {
            $cust = \Stripe\Customer::create([
                'email'    => $user->email,
                'name'     => $user->name,
                'metadata' => [
                    'platform_user_id' => $user->id,
                    'tenant_id'        => $tenant->id,
                ],
            ], [
                'stripe_account' => $tenant->stripe_account_id, // CONNECTED account
            ]);

            $map->connected_customer_id = $cust->id;
            $map->save();
        }

        $acct = \Stripe\Account::retrieve($tenant->stripe_account_id);
        $acctId = $tenant->stripe_account_id;
        // dd($acct->charges_enabled, $acct->requirements->disabled_reason, $acct->requirements->currently_due);   

        
        // ---------------------------------------------------------
        // 4) Build Checkout Session on CONNECTED account
        // ---------------------------------------------------------
        $session = CheckoutSession::create([
            'mode'      => 'subscription',
            'customer'  => $map->connected_customer_id,
            'line_items'=> [[
                'price'    => $tenant->stripe_price_id,
                'quantity' => 1,
            ]],
            'subscription_data' => [
                // Platform fee (20%)
                'application_fee_percent' => 20.0,
                'metadata' => [
                    'tenant_id'        => $tenant->id,
                    'platform_user_id' => $user->id,
                ],
            ],
            'allow_promotion_codes' => true,
            'success_url' => url("/{$tenant->id}/subscribe/success?session_id={CHECKOUT_SESSION_ID}"),
            'cancel_url'  => url("/{$tenant->id}/subscribe/cancel"),
        ], [
            'stripe_account' => $tenant->stripe_account_id, // IMPORTANT
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Success landing after checkout completes
     */
    public function success(Request $request, Tenant $tenant, Tenancy $tenancy)
    {
        // Re-initialize tenancy (safe fallback)
        if (!tenancy()->initialized) {
            $tenancy->initialize($tenant);
        }

        // You could verify session_id / load subscription details here if needed.

        // return redirect()
        //     ->route('tenant.dashboard', ['tenant' => $tenant->id])
        //     ->with('status', 'Subscription activated!');
        return view('tenant.subscribe.success', [
            'status' => 'Subscription activated!',
        ]);
    }

    /**
     * Cancel/abort landing
     */
    public function cancel(Request $request, Tenant $tenant, Tenancy $tenancy)
    {
        // Re-initialize tenancy (safe fallback)
        if (!tenancy()->initialized) {
            $tenancy->initialize($tenant);
        }

        // return back()->with('status', 'Checkout canceled.');
        return view('tenant.subscribe.cancel', [
            'status' => 'Checkout canceled.',
        ]);

    }
}
