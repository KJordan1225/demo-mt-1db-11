<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function showCheckout(Request $request, string $plan)
    {
        $plans = config('plans');
        abort_unless(isset($plans[$plan]), 404, 'Plan not found');

        $user = $request->user();

        $user->createOrGetStripeCustomer();   

        // Ensure the user is tagged to this tenant (single-DB isolation)
        if (blank($user->tenant_id)) {
            $user->tenant_id = tenant('id');
            $user->save();
        }

        // Stripe SetupIntent for Payment Element (Cashier)
        $intent = $user->createSetupIntent();

        return view('subscriptions.checkout', [
            'planKey'      => $plan,
            'plan'         => $plans[$plan],
            'clientSecret' => $intent->client_secret,
            'stripeKey'    => config('services.stripe.key'),
            'tenant'       => tenant('id'),
        ]);
    }

    public function store(Request $request, string $plan)
    {
        $plans = config('plans');
        abort_unless(isset($plans[$plan]), 404, 'Plan not found');

        $data = $request->validate([
            'payment_method' => ['required', 'string'],
        ]);

        $user = $request->user();

        $user->createOrGetStripeCustomer();   

        // Ensure we keep tenant isolation on single DB
        if (blank($user->tenant_id)) {
            $user->tenant_id = tenant('id');
            $user->save();
        }

        // Attach PM if no default
        if (! $user->hasDefaultPaymentMethod()) {
            $user->updateDefaultPaymentMethod($data['payment_method']);
        }

        try {
            // Create the subscription (Cashier)
            $subscription = $user->newSubscription('default', $plans[$plan]['price_id'])
                ->create($data['payment_method'], [
                    'metadata' => [
                        'plan_key'  => $plan,
                        'user_id'   => $user->id,
                        'tenant_id' => tenant('id'), // helpful for ops/debug
                    ],
                ]);

            // Also tag the subscription row with tenant_id (single-DB isolation)
            optional($subscription->asStripeSubscription()); // touch Stripe once (optional)
            // Refresh from DB and update tenant_id if needed:
            $fresh = $user->subscription('default');
            if ($fresh && blank($fresh->tenant_id)) {
                $fresh->tenant_id = tenant('id');
                $fresh->save();
            }

            return redirect()
                ->route('guest.account')
                ->with('status', 'Subscription started!');
        } catch (\Laravel\Cashier\Exceptions\IncompletePayment $e) {
            return redirect()->route(
                'cashier.payment',
                [$e->payment->id, 'redirect' => route('guest.account')]
            );
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function account(Request $request)
    {
        $user          = $request->user();
        $subscription  = $user->subscription('default'); // may be null

        // Optional: ensure they’re viewing within their tenant
        abort_if($user->tenant_id && $user->tenant_id !== tenant('id'), 403);

        return view('account.show', [
            'user'         => $user,
            'subscription' => $subscription,
            'tenant'       => tenant('id'),
        ]);
    }

    public function cancel(Request $request)
    {
        $sub = $request->user()->subscription('default');
        if ($sub) {
            // Optional: enforce tenant boundary
            if ($sub->tenant_id && $sub->tenant_id !== tenant('id')) {
                abort(403);
            }
            $sub->cancel();
        }
        return back()->with('status', 'Subscription cancelled (ends at period end).');
    }

    public function resume(Request $request)
    {
        $sub = $request->user()->subscription('default');
        if ($sub) {
            if ($sub->tenant_id && $sub->tenant_id !== tenant('id')) {
                abort(403);
            }
            $sub->resume();
        }
        return back()->with('status', 'Subscription resumed.');
    }
}
