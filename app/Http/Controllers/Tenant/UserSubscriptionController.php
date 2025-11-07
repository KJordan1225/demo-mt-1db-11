<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class UserSubscriptionController extends Controller
{
    /**
     * Initiates a subscription checkout session for a user.
     */
    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to subscribe.');
        }

        $creator = tenant();

        if (!$creator?->stripe_account_id || !$creator?->subscription_price_id) {
            return back()->with('error', 'Creator payment setup is incomplete. Cannot subscribe.');
        }

        try {
            // Platform Stripe client
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            $session = $stripe->checkout->sessions->create([
                'mode'       => 'subscription',
                'line_items' => [[
                    // IMPORTANT: this price must exist on the PLATFORM account
                    'price'    => $creator->subscription_price_id,
                    'quantity' => 1,
                ]],
                'customer_email' => Auth::user()->email,
                'success_url' => route('subscription.success', ['tenant' => $creator, 'session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url'  => route('subscription.cancel', ['tenant' => $creator]),

                'subscription_data' => [
                    // 20% platform fee
                    'application_fee_percent' => 20,

                    // Route the net funds to the creator’s connected account
                    'transfer_data' => [
                        'destination' => $creator->stripe_account_id, // acct_xxx
                    ],

                    'metadata' => ['tenant_id' => $creator->id],
                ],

                'metadata' => ['tenant_id' => $creator->id],
            ]);

            return redirect($session->url, 303);

        } catch (\Throwable $e) {
            \Log::error("Stripe Checkout failed for Tenant {$creator->id}: " . $e->getMessage());
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
