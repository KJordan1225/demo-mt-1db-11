<?php

namespace App\Http\Controllers;

use Stripe\Price;
use Stripe\Stripe;
use App\Models\User;
use App\Models\Subscription;
// use Stripe\Checkout\Session as CheckoutSession;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session as StripeSession;



class SubscriptionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function showPlans()
    {
        // Show the available plans (basic and premium)
        return view('subscriptions.plans');
    }

    public function subscribeBasic(Request $request)
    {
        $user = $request->user();
        $tenantId = tenant('id') ?? NULL;
        Stripe::setApiKey(config('services.stripe.secret'));

        $priceId = 'price_1SIO3gHVPMCDJGZrOlw72C3k'; // Replace with your actual Price ID for Basic Plan

        // Create a Stripe Checkout session for the basic plan
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'success_url' => route('subscribe.success'). '?session_id={CHECKOUT_SESSION_ID}&tenant_id=' . urlencode($tenantId), 
            'cancel_url' => route('subscribe.cancel'),
        ]);

        // Redirect the user to Stripe Checkout
        return redirect($checkoutSession->url);
    }

    public function subscribePremium(Request $request)
    {
        $tenantId = tenant('id') ?? NULL;
        $user = $request->user();
        Stripe::setApiKey(config('services.stripe.secret'));

        $priceId = 'price_1SMgIFHVPMCDJGZr5E1XNHsC';

        // Create a Stripe Checkout session for the premium plan
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'success_url' => route('subscribe.success'). '?session_id={CHECKOUT_SESSION_ID}&tenant_id=' . urlencode($tenantId), 
            'cancel_url' => route('subscribe.cancel'),
        ]);

        // Redirect the user to Stripe Checkout
        return redirect($checkoutSession->url);
    }

     // Handle success after successful payment
    public function success(Request $request)
    {
        $user = auth()->user();

        // 1) Get params from the request
        $sessionId = $request->query('session_id');
        $tenantId  = $request->query('tenant_id'); // or $request->route('tenant')
     
        // dd('Session ID: ' . $sessionId . ', Tenant ID: ' . $tenantId);
        // 2) Retrieve the Checkout Session to get the real subscription id
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = StripeSession::retrieve($sessionId);

        // session->subscription is the real subscription id (e.g. sub_XXXXXXXX)
        $stripeSubscriptionId = $session->subscription ?? null;
        abort_unless($stripeSubscriptionId, 400, 'No subscription found on session.');

        // 3) Persist in your DB
        $subscription = new \App\Models\Subscription([
            'user_id'                 => $user->id,
            'tenant_id'               => $tenantId,                // make sure you actually send this
            'stripe_subscription_id'  => $stripeSubscriptionId,    // NOT the session id
            'status'                  => 'active',
        ]);
        $subscription->save();

        return view('subscriptions.success');
    }


    public function cancel()
    {
        return view('subscriptions.cancel');
    }
}
