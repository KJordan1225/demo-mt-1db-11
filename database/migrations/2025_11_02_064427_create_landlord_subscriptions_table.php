<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('landlord_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_customer_id')->index();
            $table->string('stripe_subscription_id')->index();
            $table->string('status')->index(); // trialing|active|past_due|canceled|unpaid|incomplete|incomplete_expired
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestamp('current_period_end')->nullable();
            $table->json('raw')->nullable(); // store raw Stripe sub for audit
            $table->timestamps();

            // landlord scope: no tenant column (or explicitly null if you prefer)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landlord_subscriptions');
    }
};
