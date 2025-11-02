<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;
use App\Models\LandlordSubscription;
use Illuminate\Support\Facades\Auth;

class LandlordSubscribeController extends Controller
{
    public function showForm(Request $request)
    {        
        // Show details for the landlord plan from env/config
        return view('subscriptions.landlord-subscribe', [
            'priceId'   => config('services.stripe.landlord_price_id') ?? env('STRIPE_LANDLORD_PRICE_ID'),
            'productId' => config('services.stripe.landlord_product_id') ?? env('STRIPE_LANDLORD_PRODUCT_ID'),
        ]);
    }

    public function startCheckout(Request $request)
    {
        $request->validate([
            // optionally accept coupon, quantity, etc.
        ]);

        $stripe = new StripeClient(config('services.stripe.secret'));
        $user   = Auth::user();

        // 1) Ensure/retrieve a Customer on landlord account
        $customerId = $user->stripe_customer_id ?? null; // add this nullable column to users if helpful
        if (!$customerId) {
            $customer = $stripe->customers->create([
                'email' => $user->email,
                'name'  => $user->name,
                'metadata' => [
                    'user_id'  => $user->id,
                    'tenant_id'=> tenant('id') ?? 'n/a',
                ],
            ]);
            $customerId = $customer->id;
            // persist if you added the column on users table
            if (\Schema::hasColumn('users', 'stripe_customer_id')) {
                $user->forceFill(['stripe_customer_id' => $customerId])->save();
            }
        }

        // 2) Create Checkout Session for landlord price (platform account)
        $priceId = env('STRIPE_LANDLORD_PRICE_ID');
        $session = $stripe->checkout->sessions->create([
            'mode'        => 'subscription',
            'customer'    => $customerId,
            'line_items'  => [[ 'price' => $priceId, 'quantity' => 1 ]],
            'success_url' => route('landlord.subscribe.success', ['tenant' => tenant('id')]).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('landlord.subscribe.cancel',  ['tenant' => tenant('id')]),
            'metadata'    => [
                'user_id'   => $user->id,
                'tenant_id' => tenant('id') ?? 'n/a',
            ],
            // Optional niceties:
            // 'allow_promotion_codes' => true,
            // 'billing_address_collection' => 'required',
            // 'subscription_data' => ['trial_period_days' => 7],
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        // Show a nice success page; actual persistence is done in webhook
        return redirect()
            ->route('tenant.dashboard', ['tenant' => tenant('id')])
            ->with('success', 'Thanks! Your landlord subscription is being activated.');
    }

    public function cancel(Request $request)
    {
        return redirect()
            ->route('tenant.dashboard', ['tenant' => tenant('id')])
            ->with('error', 'Subscription was canceled before completing payment.');
    }
}
