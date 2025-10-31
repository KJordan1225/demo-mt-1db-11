<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Stripe;

class CreatorSubscriptionController extends Controller
{
    public function createSubscription(Request $request, string $tenantId)
    {
        if (!tenancy()->initialized) { tenancy()->initialize($tenantId); }     // DIFFERENCE

        $tenant = tenancy()->tenant();

        if (!$tenant->stripe_account_id || !$tenant->stripe_price_id) {
            return back()->with('error', 'Creator is not ready for subscriptions (missing Stripe setup).');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = CheckoutSession::create([
                'mode'   => 'subscription',
                'line_items' => [[
                    'price'    => $tenant->stripe_price_id,                   // DIFFERENCE: use prebuilt Price
                    'quantity' => 1,
                ]],
                'success_url' => route('subscription.success', ['tenant' => $tenant->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('subscription.cancel',  ['tenant' => $tenant->id]),

                // IMPORTANT: For Connect + subscriptions, attach transfer_data on subscription_data
                'subscription_data' => [                                       // DIFFERENCE
                    'transfer_data' => [
                        'destination' => $tenant->stripe_account_id,
                    ],
                    // Choose one platform fee strategy:
                    // 'application_fee_percent' => 10.0,                     // e.g., 10% fee
                    // or:
                    // 'application_fee_amount'  => 100,                      // e.g., $1.00 in cents
                ],
            ]);

            return redirect($session->url);
        } catch (\Throwable $e) {
            return back()->with('error', 'Error creating checkout session: ' . $e->getMessage());
        }
    }

    public function handleCheckoutSession(Request $request, string $tenantId)
    {
        if (!tenancy()->initialized) { tenancy()->initialize($tenantId); }     // DIFFERENCE

        $sessionId = $request->string('session_id');
        if ($sessionId->isEmpty()) {
            return back()->with('error', 'Missing session_id.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $stripeSubscriptionId = $session->subscription;

            $tenant = tenancy()->tenant();

            Subscription::create([
                'user_id'                => auth()->id(),
                'tenant_id'              => $tenant->id,
                'stripe_subscription_id' => $stripeSubscriptionId,
            ]);

            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->id])
                ->with('success', 'You have successfully subscribed!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to finalize subscription: ' . $e->getMessage());
        }
    }

    public function cancelSubscription(Request $request, string $tenantId)
    {
        if (!tenancy()->initialized) { tenancy()->initialize($tenantId); }     // DIFFERENCE
        return redirect()->route('tenant.dashboard', ['tenant' => $tenantId])->with('error', 'Subscription canceled');
    }
}
