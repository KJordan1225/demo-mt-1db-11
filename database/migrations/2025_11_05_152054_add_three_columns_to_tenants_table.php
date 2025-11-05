<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Force the central connection used by stancl/tenancy.
     * Adjust if your central connection name differs.
     */
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Store the creator-entered temporary price (in cents)
            $table->unsignedInteger('temporary_price_cents')->nullable()->after('data');

            // Stripe identifiers (usually "prod_xxx" and "price_xxx")
            $table->string('stripe_product_id', 100)->nullable()->after('temporary_price_cents')->index();
            $table->string('stripe_price_id', 100)->nullable()->after('stripe_product_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'temporary_price_cents',
                'stripe_product_id',
                'stripe_price_id',
            ]);
        });
    }
};
