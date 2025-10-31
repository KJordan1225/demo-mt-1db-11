<?php

namespace App\Services;

use App\Models\Tenant;
use Stripe\Account;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class StripeService
{
    public function createStripeConnectAccount(Tenant $tenant)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $account = Account::create([
            'type'          => 'express',                         // DIFFERENCE (express is typical for marketplaces)
            'country'       => 'US',
            'email'         => $tenant->email,
            'business_type' => 'individual',
            'metadata'      => ['tenant_id' => (string) $tenant->id],
        ]);

        $tenant->stripe_account_id = $account->id;
        $tenant->save();

        return $account;
    }

    /**
     * Ensure a Product & Price exist on the creator's connected account
     * and return the active price ID. Creates/rotates as needed.
     */
    public function ensureCreatorPrice(Tenant $tenant): string
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        if (!$tenant->stripe_account_id) {
            throw new \RuntimeException('Creator is not connected to Stripe.');
        }
        if (!$tenant->plan_name || !$tenant->plan_amount_cents) {
            throw new \InvalidArgumentException('Creator plan_name and plan_amount_cents are required.');
        }

        $account = $tenant->stripe_account_id;
        $stripeOpts = ['stripe_account' => $account]; // DIFFERENCE: operate on connected account

        // Ensure Product
        if (!$tenant->stripe_product_id) {
            $product = Product::create([
                'name'     => $tenant->plan_name,
                'metadata' => ['tenant_id' => (string) $tenant->id],
            ], $stripeOpts);

            $tenant->stripe_product_id = $product->id;
            $tenant->save();
        } else {
            // Optional: keep product name in sync
            Product::update(
                $tenant->stripe_product_id,
                ['name' => $tenant->plan_name],
                $stripeOpts
            );
        }

        // If price already exists but amount/currency/interval changed,
        // create a new price (Stripe prices are immutable for amount/interval).
        $needsNewPrice = true;
        if ($tenant->stripe_price_id) {
            try {
                $existing = Price::retrieve($tenant->stripe_price_id, $stripeOpts);
                $sameAmount   = ((int)$existing->unit_amount) === (int)$tenant->plan_amount_cents;
                $sameCurrency = $existing->currency === strtolower($tenant->plan_currency ?? 'usd');
                $sameInterval = ($existing->recurring->interval ?? null) === $tenant->plan_interval;

                if ($sameAmount && $sameCurrency && $sameInterval && $existing->active) {
                    $needsNewPrice = false;
                } else {
                    // Deactivate old price to avoid accidental use
                    Price::update($existing->id, ['active' => false], $stripeOpts);
                }
            } catch (\Throwable $e) {
                // fall-through to create new price
            }
        }

        if ($needsNewPrice) {
            $price = Price::create([
                'product'    => $tenant->stripe_product_id,
                'currency'   => strtolower($tenant->plan_currency ?? 'usd'),
                'unit_amount'=> (int) $tenant->plan_amount_cents,
                'recurring'  => ['interval' => $tenant->plan_interval ?: 'month'],
                'metadata'   => ['tenant_id' => (string) $tenant->id],
            ], $stripeOpts);

            $tenant->stripe_price_id = $price->id;
            $tenant->save();
        }

        return $tenant->stripe_price_id;
    }
}
