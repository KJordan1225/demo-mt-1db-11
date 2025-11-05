<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;

class SubscriptionController extends Controller
{
    /**
     * Initiates a subscription checkout session for a user.
     */
    public function checkout(Request $request)
    {
        // 1. Get the current tenant (creator)
        $creator = tenant();

        // 2. Validation
        if (!$creator->stripe_account_id || !$creator->subscription_price_id) {
            return back()->with('error', 'Creator payment setup is incomplete. Cannot subscribe.');
        }

        // 3. Instantiate the Stripe client, authenticated as the creator
        // We use the platform client and specify the connected account later
        $stripe = Tenant::platformStripeClient();

        // Platform fee configuration (e.g., 10% commission on a $9.99 charge)
        $platformFeeAmount = 100; // $1.00 USD (Approx. 10%)

        try {
            // 4. Create a Stripe Checkout Session
            $session = $stripe->checkout->sessions->create([
                'mode' => 'subscription',
                'line_items' => [
                    [
                        'price' => $creator->subscription_price_id, // Price ID from creator's account
                        'quantity' => 1,
                    ],
                ],
                'success_url' => route('subscription.success', ['tenant' => $creator->id, 'session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url' => route('subscription.cancel', ['tenant' => $creator->id]),
                
                // Destination Charge: Charge is created on the Platform, then transferred
                // to the Connected Account, minus the platform fee.
                'payment_intent_data' => [
                    'application_fee_amount' => $platformFeeAmount,
                    'transfer_data' => [
                        'destination' => $creator->stripe_account_id, // Target creator's account
                    ],
                ],
            ]);

            // 5. Redirect the user to Stripe Checkout
            return redirect()->away($session->url);

        } catch (ApiErrorException $e) {
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful subscription. (You would add database logic here)
     */
    public function success(Request $request)
    {
        // In a real app, you would verify the session ID here and record the subscription
        return view('tenant.success');
    }

    /**
     * Handle cancelled checkout.
     */
    public function cancel()
    {
        return back()->with('info', 'Subscription process cancelled.');
    }
}
