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
        // 1. Basic validation and authentication
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to subscribe.');
        }
        
        // 1. Get the current tenant (creator)
        $creator = tenant();

        // 2. Validation
        if (!$creator->stripe_account_id || !$creator->subscription_price_id) {
            return back()->with('error', 'Creator payment setup is incomplete. Cannot subscribe.');
        }
        
        try {
            // 3. Authenticate the Stripe API client as the CONNECTED CREATOR ACCOUNT
            // This is the core fix for the "No such price" error.
            $stripe = $creator->tenantStripe(); // Uses the Stripe-Account header
            $applicationFeePercent = 10.0; // Platform fee percentage
            // 4. Create the Checkout Session on the CONNECTED account
            $session = $stripe->checkout->sessions->create([
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $creator->subscription_price_id, // This Price ID now correctly exists on the connected account
                    'quantity' => 1,
                ]],

                // Define the fee to be collected by your Platform
                'subscription_data' => [
                    'application_fee_percent' => $applicationFeePercent,
                ],

                // Customer and URLs
                'customer_email' => Auth::user()->email,
                'success_url' => route('subscription.success', ['tenant' => $creator, 'session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url' => route('subscription.cancel', ['tenant' => $creator]),
            ]);

            return redirect($session->url, 303);

        } catch (\Exception $e) {
            // Log error for debugging
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
