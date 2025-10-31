<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Creator-facing plan meta
            $table->string('plan_name')->nullable()->after('email');             // DIFFERENCE
            $table->string('plan_currency', 10)->default('usd')->after('plan_name'); // DIFFERENCE
            $table->unsignedInteger('plan_amount_cents')->nullable()->after('plan_currency'); // DIFFERENCE
            $table->string('plan_interval')->default('month')->after('plan_amount_cents');    // DIFFERENCE (month|year)

            // Stripe objects on the connected account
            $table->string('stripe_product_id')->nullable()->after('stripe_account_id'); // DIFFERENCE
            $table->string('stripe_price_id')->nullable()->after('stripe_product_id');   // DIFFERENCE
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'plan_name',
                'plan_currency',
                'plan_amount_cents',
                'plan_interval',
                'stripe_product_id',
                'stripe_price_id',
            ]);
        });
    }

};