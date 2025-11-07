<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carousel_posts', function (Blueprint $table) {
            $table->id();

            // String FK to tenants.id (nullable)
            $table->string('tenant_id')->nullable();

            // Fields
            $table->string('title');                 // required

            $table->timestamps();

            // Index + FK constraint to tenants(id)
            $table->index('tenant_id');
            $table->foreign('tenant_id')
                  ->references('id')->on('tenants')
                  ->cascadeOnUpdate()
                  ->nullOnDelete(); // if tenant deleted, set tenant_id to null
        });
    }

    public function down(): void
    {
        Schema::table('carousel_posts', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
        });

        Schema::dropIfExists('carousel_posts');
    }
};
