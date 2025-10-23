<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Tenant scope (single-DB stancl/tenancy)
            $table->string('tenant_id', 191)->index();

            // Author
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Content
            $table->string('title', 255);
            $table->longText('body')->nullable();

            // Publication time
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            // $table->softDeletes(); // uncomment if you want soft deletes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
