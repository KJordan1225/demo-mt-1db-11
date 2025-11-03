<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stancl's tenancy uses the 'tenants' table. We are modifying it.
        Schema::table('tenants', function (Blueprint $table) {
            // Stores the unique ID for the creator's Stripe Connect Account (acct_...)
            $table->string('stripe_account_id')->nullable()->unique();
            
            // Stores the price ID for the creator's main subscription (price_...)
            $table->string('subscription_price_id')->nullable(); 

            // Stores the link for the creator to complete KYC/bank details
            $table->text('onboarding_link')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('stripe_account_id');
            $table->dropColumn('subscription_price_id');
            $table->dropColumn('onboarding_link');
        });
    }
};
