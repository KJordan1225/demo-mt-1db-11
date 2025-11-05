<?php

namespace App\Http\Controllers\Central;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Redirect;


class CreatorOnboardingController extends Controller
{
    
    /**
     * Display the creator dashboard/onboarding status view.
     */
    public function showDashboard()
    {
        // This view will either show the 'Connect' button or the 'Set Price' button
        return view('central.creator-payment-setup');
    }


    /**
     * Route to show the price setting form.
     */
    public function showPriceSetup(Tenant $tenant)
    {
        // Ensure the logged-in user owns this tenant before showing the form
        abort_unless(Auth::check(), 403);
        abort_unless(Auth::user()->tenant_id === $tenant->getKey(), 403);

        // dd($tenant);
        return view('central.price-setup', ['tenant' => $tenant]);
        // Pass a SINGLE model to the view (named $tenant)
        // return view('central.price-setup', compact('tenant'));
    }



    /**
     * Handle the price submission, create Stripe Product/Price on connected account.
     */
    public function storeSubscriptionPrice(Request $request, Tenant $tenant)
    {
        // 1. Authorization check
        if (Auth::user()->tenant_id !== $tenant->id || empty($tenant->stripe_account_id)) {
            return Redirect::back()->with('error', 'Authentication or Stripe account status invalid.');
        }

        // 2. Validation
        $data = $request->validate([
            'price' => 'required|numeric|min:1.00',
        ]);
        
        $amountInCents = round($data['price'] * 100);

        try {
            // Use the helper method to run API calls on the connected account
            $stripe = $tenant->tenantStripe();

            // 3. Create a Product on the creator's connected account
            // This is a single, generic product for all their subscriptions
            if (!$tenant->stripe_product_id) {
                $product = $stripe->products->create([
                    'name' => "{$tenant->id}'s Exclusive Content Access",
                    'type' => 'service',
                ]);
                $tenant->stripe_product_id = $product->id;
            }

            // 4. Create a Price on the creator's connected account (or archive old one and create new)
            // Stripe recommends creating a new Price object instead of updating an old one.
            $price = $stripe->prices->create([
                'product' => $tenant->stripe_product_id,
                'unit_amount' => $amountInCents,
                'currency' => 'usd',
                'recurring' => ['interval' => 'month'],
                // Set the price nickname for easy identification
                'nickname' => 'Monthly Subscription ' . $data['price'], 
            ]);

            // 5. Update the central Tenant model
            $tenant->monthly_price = $data['price'];
            $tenant->subscription_price_id = $price->id;
            $tenant->save();

            return Redirect::back()->with('status', 'Subscription price successfully created on Stripe!');

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return Redirect::back()->with('error', 'Stripe API Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
    
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