<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use App\Models\User;
use App\Models\LandlordSubscription;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        // (Optional but recommended) verify signature
        // $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        // $sig = $request->header('Stripe-Signature');
        // $event = \Stripe\Webhook::constructEvent($request->getContent(), $sig, $endpointSecret);

        $payload = $request->all();
        $type    = $payload['type'] ?? null;
        $data    = $payload['data']['object'] ?? [];

        switch ($type) {
            case 'checkout.session.completed':
                // Subscriptions are created; session contains subscription + customer
                if (($data['mode'] ?? null) === 'subscription') {
                    $this->upsertSubscriptionFromSession($data);
                }
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->syncSubscription($data);
                break;

            case 'customer.subscription.deleted':
                $this->syncSubscription($data); // will mark canceled
                break;
        }

        return response()->noContent();
    }

    protected function upsertSubscriptionFromSession(array $session): void
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $subscriptionId = $session['subscription'] ?? null;
        $customerId     = $session['customer'] ?? null;

        if (!$subscriptionId || !$customerId) return;

        // We need our app's user
        $email = $session['customer_details']['email'] ?? null;
        $user  = $email ? User::where('email', $email)->first() : null;
        if (!$user) return;

        // Fetch full subscription
        $sub = $stripe->subscriptions->retrieve($subscriptionId, []);
        $this->persistSub($user, $customerId, $sub);
    }

    protected function syncSubscription(array $stripeSub): void
    {
        $customerId = $stripeSub['customer'] ?? null;
        if (!$customerId) return;

        $user = User::where('stripe_customer_id', $customerId)->first();
        if (!$user) {
            // fallback: try email on items' latest invoice (extra call if needed)
            return;
        }

        $this->persistSub($user, $customerId, (object) $stripeSub);
    }

    protected function persistSub(User $user, string $customerId, object $sub): void
    {
        LandlordSubscription::updateOrCreate(
            ['stripe_subscription_id' => $sub->id],
            [
                'user_id'              => $user->id,
                'stripe_customer_id'   => $customerId,
                'status'               => $sub->status,
                'cancel_at_period_end' => (bool) ($sub->cancel_at_period_end ?? false),
                'current_period_end'   => isset($sub->current_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($sub->current_period_end)
                    : null,
                'raw'                  => $sub,
            ]
        );
    }
}
