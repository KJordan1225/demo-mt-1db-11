<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add stripe_id and stripe_subscription_id columns
            $table->string('stripe_id')->nullable()->after('email');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop stripe_id and stripe_subscription_id columns
            $table->dropColumn('stripe_id');
            $table->dropColumn('stripe_subscription_id');
        });
    }
}
