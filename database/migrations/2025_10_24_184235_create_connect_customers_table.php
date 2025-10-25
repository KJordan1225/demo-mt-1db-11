<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('connect_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tenant_id', 191)->index(); // stancl tenant key
            $table->string('connected_customer_id');   // customer id on connected acct
            $table->timestamps();
            $table->unique(['user_id','tenant_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('connect_customers');
    }
};
