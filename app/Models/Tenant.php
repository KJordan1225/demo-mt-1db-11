<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $casts = ['data' => 'array'];protected 
    
    $fillable = [
        'id', 
        'data', 
        'stripe_account_id', 
        'subscription_price_id', 
        'onboarding_link'
    ];

    // Stancl v3 configuration
    public static function getConfigKeys(): array
    {
        return [
            'stripe_account_id', 
            'subscription_price_id',
        ];
    }

    /**
     * Get a StripeClient instance authenticated as this connected account.
     * Use this for creating Products, Prices, and Subscriptions for the creator.
     *
     * @return StripeClient|null
     */
    public function stripeClientAsCreator(): ?StripeClient
    {
        if (!$this->stripe_account_id) {
            return null;
        }

        // Instantiate Stripe client using platform secret key
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        // Set the Stripe-Account header to authenticate as the connected account
        $stripe->setApiVersion('2020-08-27'); // Use a stable API version
        $stripe->setConnectAccount($this->stripe_account_id);

        return $stripe;
    }

    /**
     * Get the default StripeClient instance (authenticated as the platform).
     */
    public static function platformStripeClient(): StripeClient
    {
        return new StripeClient(env('STRIPE_SECRET'));
    }
}