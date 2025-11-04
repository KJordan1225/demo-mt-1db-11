<?php

namespace App\Models;

use Stripe\StripeClient;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $casts = ['data' => 'array'];

    protected $guarded = [];   
    

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
    public function stripeClientAsCreator(string $stripeId): ?StripeClient
    {
        if (!$stripeId) {
            return null;
        }

        // Instantiate Stripe client using platform secret key
        // $stripe = new StripeClient(env('STRIPE_SECRET'));

        // Set the Stripe-Account header to authenticate as the connected account
        // $stripe->setApiVersion('2020-08-27'); 
        $stripe = new StripeClient([
            'api_key'        => config('services.stripe.secret'),
            'stripe_version' => '2020-08-27',   // your desired API version
        ]);// Use a stable API version
        // $stripe->setConnectAccount($this->stripe_account_id);

        return $stripe;
    }

    /**
     * Get the default StripeClient instance (authenticated as the platform).
     */
    public static function platformStripeClient(): StripeClient
    {
        return new StripeClient(env('STRIPE_SECRET'));
    }

    public function tenantStripe(): StripeClient
    {
        if (empty($this->stripe_account_id)) {
            throw new \Exception("Tenant '{$this->id}' does not have a connected Stripe account ID.");
        }

        // Initialize the Stripe Client using the Platform's secret key
        // BUT specify the Stripe-Account header using the connected ID.
        // This directs the API call to run against the connected account.
        $stripe = new StripeClient([
            'api_key' => config('services.stripe.secret'),
            'stripe_version' => '2020-08-27', // Use a stable Stripe API version
            'stripe_account' => $this->stripe_account_id, // Authenticates as the connected account
        ]);

        return $stripe;
    }
}