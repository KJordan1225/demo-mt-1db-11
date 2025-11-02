<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\StripeClient;

class CreateLandlordStripePlan extends Command
{
    protected $signature = 'stripe:create-landlord-plan {name=Landlord Pro} {amount=999} {interval=month} {currency=usd}';
    protected $description = 'Create landlord product & price and print IDs';

    public function handle(): int
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $name     = (string) $this->argument('name');
        $amount   = (int) $this->argument('amount');
        $interval = (string) $this->argument('interval');
        $currency = strtolower((string) $this->argument('currency'));

        $product = $stripe->products->create(['name' => $name]);
        $price   = $stripe->prices->create([
            'currency'    => $currency,
            'unit_amount' => $amount,
            'recurring'   => ['interval' => $interval],
            'product'     => $product->id,
        ]);

        $this->info("Product: {$product->id}");
        $this->info("Price:   {$price->id}");
        $this->warn('Put these into .env as STRIPE_LANDLORD_PRODUCT_ID / STRIPE_LANDLORD_PRICE_ID');

        return self::SUCCESS;
    }
}
