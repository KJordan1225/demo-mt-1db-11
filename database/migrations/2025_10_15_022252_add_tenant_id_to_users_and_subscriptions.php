<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void {
        if (!Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function ($t) {
                $t->string('tenant_id')->nullable()->index();
            });
        }
        
        Schema::table('subscriptions', function ($t) {
            $t->string('tenant_id')->nullable()->default('landlord')->index();
        });
        
    }
    public function down(): void {
        \Schema::table('subscriptions', fn($t) => $t->dropColumn('tenant_id'));
        \Schema::table('users', fn($t) => $t->dropColumn('tenant_id'));
    }
};
