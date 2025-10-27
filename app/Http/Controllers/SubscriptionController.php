<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

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
        Stripe::setApiKey(config('services.stripe.secret'));

        // Create a Stripe Checkout session for the basic plan
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Basic Plan',
                        ],
                        'unit_amount' => 500, // $5 in cents
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'success_url' => route('subscribe.success'),
            'cancel_url' => route('subscribe.cancel'),
        ]);

        // Redirect the user to Stripe Checkout
        return redirect($checkoutSession->url);
    }

    public function subscribePremium(Request $request)
    {
        $user = $request->user();
        Stripe::setApiKey(config('services.stripe.secret'));

        // Create a Stripe Checkout session for the premium plan
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Premium Plan',
                        ],
                        'unit_amount' => 1000, // $10 in cents
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'success_url' => route('subscribe.success'),
            'cancel_url' => route('subscribe.cancel'),
        ]);

        // Redirect the user to Stripe Checkout
        return redirect($checkoutSession->url);
    }

    public function success()
    {
        return view('subscriptions.success');
    }

    public function cancel()
    {
        return view('subscriptions.cancel');
    }
}
