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
        Schema::table('tenants', function (Blueprint $table) {
           if (!Schema::hasColumn('tenants', 'stripe_payouts_enabled')) {
                $table->boolean('stripe_payouts_enabled')->default(false)->after('stripe_account_id');
            }

            if (!Schema::hasColumn('tenants', 'stripe_details_submitted')) {
                $table->boolean('stripe_details_submitted')->default(false)->after('stripe_payouts_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'stripe_details_submitted')) {
                $table->dropColumn('stripe_details_submitted');
            }
            if (Schema::hasColumn('tenants', 'stripe_payouts_enabled')) {
                $table->dropColumn('stripe_payouts_enabled');
            }
        });
    }
};