<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User this subscription belongs to
            
            // Change tenant_id to string and remove foreign key constraint
            $table->string('tenant_id')->nullabe(); // Tenant this subscription is for (now a string)
            
            $table->string('stripe_subscription_id'); // Stripe subscription ID
            $table->enum('status', ['active', 'canceled', 'past_due'])->default('active'); // Subscription status
            $table->timestamp('ends_at')->nullable(); // When the subscription ends
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
