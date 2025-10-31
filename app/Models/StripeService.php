<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Account;
use App\Models\Tenant;

class StripeService
{
    /**
     * Create a Stripe Connect account for a new creator (tenant).
     *
     * @param Tenant $tenant
     * @return \Stripe\Account
     */
    public function createStripeConnectAccount(Tenant $tenant)
    {
        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create a Stripe account for the creator (tenant)
            $account = Account::create([
                'type' => 'standard',  // Choose 'standard', 'express', or 'custom' depending on your scenario
                'country' => 'US',     // Set country based on your creator's location
                'email' => $tenant->email, // Use tenant's email for the Stripe account
                'business_type' => 'individual',  // Adjust based on the type of creator (individual or company)
                'metadata' => [
                    'tenant_id' => $tenant->id,
                ],
            ]);

            // Save the Stripe account ID to the tenant model
            $tenant->stripe_account_id = $account->id;
            $tenant->save();

            return $account;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            throw new \Exception('Stripe Account creation failed: ' . $e->getMessage());
        }
    }
}
