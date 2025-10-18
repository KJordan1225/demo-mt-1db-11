<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Roles: tenant_id NULL => landlord; non-NULL => tenant-scoped
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name');                 // display name, e.g. 'Super Admin'
            $t->string('slug');                 // machine name, e.g. 'super_admin'
            $t->enum('scope', ['landlord','tenant'])->index();
            $t->string('tenant_id')->nullable()->index();
            $t->timestamps();
            $t->unique(['slug','scope','tenant_id']); // prevents dupes per scope+tenant
        });

        Schema::create('role_user', function (Blueprint $t) {
            $t->id();
            $t->foreignId('role_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('tenant_id')->nullable()->index(); // mirror context
            $t->timestamps();
            $t->unique(['role_id','user_id','tenant_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
