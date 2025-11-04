<?php

namespace App\Http\Controllers\Central;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;


class CreatorOnboardingController extends Controller
{
    /**
     * Initiate the Stripe Connect account creation process for a given Tenant.
     * This assumes the Tenant already exists and is logged into the central app.
     */
    public function createStripeAccount(Tenant $tenant)
    {
       
        try {
            $tenantId = auth()->user()->tenant_id;
            $tenant = \App\Models\Tenant::on(config('tenancy.database.central_connection'))->findOrFail($tenantId);
            // 1. Create a Stripe Express/Standard Account
            $account = Tenant::platformStripeClient()->accounts->create([
                'type' => 'express', // Recommended for creator platforms
                'country' => 'US',   // Adjust country as needed
                'email' => $tenant->data['creator_email'] ?? "creator-{$tenant->id}@platform.com",
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'business_type' => 'individual',
            ]);
            
            $tenantId = auth()->user()->tenant_id;
            $tenant = \App\Models\Tenant::on(config('tenancy.database.central_connection'))->findOrFail($tenantId);
            $tenant->stripe_account_id = $account->id;  // <-- column
            $tenant->save();
            
            // 2. Create an Account Link (Onboarding URL)
            $refreshUrl = route('stripe.connect.refresh', ['tenant' => $tenant->id]);
            $returnUrl = route('stripe.connect.return', ['tenant' => $tenant->id]);

            $accountLink = Tenant::platformStripeClient()->accountLinks->create([
                'account' => $account->id,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);
            $tenantId = auth()->user()->tenant_id;
            $tenant = \App\Models\Tenant::on(config('tenancy.database.central_connection'))->findOrFail($tenantId);
            $tenant->onboarding_link = $accountLink->url;  // <-- column
            $tenant->save();            
            
            // Redirect the creator to Stripe to complete onboarding
            return redirect()->away($accountLink->url);

        } catch (ApiErrorException $e) {
            Log::error("Stripe Account Creation failed for Tenant {$tenant->id}: " . $e->getMessage());
            return back()->with('error', 'Stripe connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the redirect back from Stripe after the creator completes onboarding.
     * This is where we create the subscription product/price on their connected account.
     */
    public function handleOauthRedirect(Request $request, Tenant $tenant)
    {
        // Check if the onboarding was successful
        if ($request->get('error')) {
            return redirect()->route('central.dashboard')->with('error', 'Stripe onboarding was cancelled or failed.');
        }

        // Fetch the account status
        try {
            $account = Tenant::platformStripeClient()->accounts->retrieve($tenant->stripe_account_id);

            if ($account->details_submitted) {
                // Account is successfully onboarded, now create the subscription product and price.
                $this->createSubscriptionProductAndPrice($tenant);

                return redirect()->route('central.dashboard')->with('success', 'Stripe account successfully connected and subscription product created!');
            }

            return redirect()->route('central.dashboard')->with('warning', 'Stripe account connected, but details still pending verification.');

        } catch (ApiErrorException $e) {
            Log::error("Stripe Oauth Redirect failed for Tenant {$tenant->id}: " . $e->getMessage());
            return redirect()->route('central.dashboard')->with('error', 'Error retrieving Stripe account status.');
        }
    }

    /**
     * Helper to create the Subscription Product and Price ON THE CONNECTED ACCOUNT.
     * This ensures the creator owns the subscription object and receives the funds.
     */
    protected function createSubscriptionProductAndPrice(Tenant $tenant)
    {
        
        try {
            $tenantId = auth()->user()->tenant_id;            
            $tenant = \App\Models\Tenant::on(config('tenancy.database.central_connection'))->findOrFail($tenantId);
            // dd($tenant->stripe_account_id);
            $stripeId = $tenant->stripe_account_id; 
            // dd($stripeId);
            $stripe = $tenant->stripeClientAsCreator($stripeId);

            // 1. Create a Product on the creator's connected account
            $product = $stripe->products->create([
                'name' => 'Monthly Exclusive Access',
                'description' => "Subscription to {$tenant->id}'s exclusive content.",
                'metadata' => ['tenant_id' => $tenant->id],
            ],
                ['stripe_account' => $tenant->stripe_account_id] // Specify connected account
            );

            // 2. Create a Price for that Product (e.g., $9.99/month)
            $price = $stripe->prices->create([
                'unit_amount' => 999, // $9.99 USD
                'currency' => 'usd',
                'recurring' => ['interval' => 'month'],
                'product' => $product->id,
            ],
                ['stripe_account' => $tenant->stripe_account_id] // Specify connected account
            );

            // 3. Store the Price ID on the central Tenant model
            $tenantId = auth()->user()->tenant_id;
            $tenant = \App\Models\Tenant::on(config('tenancy.database.central_connection'))->findOrFail($tenantId);
            $tenant->subscription_price_id = $price->id;  // <-- column
            $tenant->save(); 

        } catch (ApiErrorException $e) {
            Log::error("Failed to create product/price on connected account {$tenant->id}: " . $e->getMessage());
            throw $e; // Re-throw to be caught in the main handler
        }
    }

    /**
     * Route for Stripe to refresh the onboarding link if it expires.
     */
    public function handleOauthRefresh(Tenant $tenant)
    {
        return $this->createStripeAccount($tenant);
    }
}