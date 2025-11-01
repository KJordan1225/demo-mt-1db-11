<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use Stancl\Tenancy\Facades\Tenancy;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Subscription as StripeSubscription;
use App\Models\Tenant;  // Creator (tenant) model

class UserSubscriptionController extends Controller
{
    public function createSubscription(Request $request)
    {
        $tenantId = $request->tenant_id; 
        // Manually initialize tenancy in case it's not already initialized
        if (!tenancy()->initialized) {
            tenancy()->initialize($tenantId);
        }

        // Set the Stripe secret key for the connected account
        $tenant = tenant();  // Get the current tenant (creator)
        $stripeAccountId = $tenant->stripe_account_id;  // Get Stripe account ID from tenant

        // Set the API key to the Stripe connected account using Stripe Connect
        Stripe::setApiKey(env('STRIPE_SECRET'));
        // Create the Stripe Checkout session for the subscription
        try {
            $priceId = $tenant->stripe_price_id;
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $priceId,  // Use the existing price ID associated with the tenant's subscription
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'success_url' => route('usersubscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('usersubscription.cancel'),
                                
            ]);

            return redirect($session->url);  // Redirect to Stripe Checkout
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating subscription: ' . $e->getMessage());
        }
    }

    public function handleCheckoutSession(Request $request)
    {
        $sessionId = $request->input('session_id');
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = StripeSession::retrieve($sessionId);

        // Get the user's subscription details from Stripe
        $stripeSubscription = StripeSubscription::retrieve($session->subscription);

        // Retrieve the tenant (creator) information
        $tenant = tenant();

        // Create the subscription record in your database
        $subscription = new UserSubscription();
        $subscription->user_id = auth()->id();  // Get the logged-in user ID
        $subscription->tenant_id = $tenant->id;  // Store the tenant (creator) ID
        $subscription->stripe_subscription_id = $stripeSubscription->id;
        $subscription->save();

        // Redirect to the success page
        return redirect()->route('tenant.dashboard')->with('success', 'You have successfully subscribed!');
    }

    public function cancelSubscription(Request $request)
    {
        return redirect()->route('tenant.dashboard')->with('error', 'Subscription canceled');
    }
}
