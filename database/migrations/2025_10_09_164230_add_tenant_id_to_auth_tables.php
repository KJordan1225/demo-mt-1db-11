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
        // users
        Schema::table('users', function (Blueprint $table) {
            $table->string('tenant_id')->index()->after('id');
            // optional: enforce tenant-unique email
            $table->unique(['tenant_id', 'email']);
        });

        // password_reset_tokens
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->string('tenant_id')->index()->after('email');
            // NOTE: This table already has a PRIMARY KEY on 'email'.
            // If you want per-tenant uniqueness instead, you would need to
            // drop the existing primary and create a composite primary:
            //
            $table->dropPrimary('password_reset_tokens_email_primary');
            $table->primary(['tenant_id', 'email']);
        });

        // sessions
        Schema::table('sessions', function (Blueprint $table) {
            $table->string('tenant_id')->index()->after('id');
            // You can also make (tenant_id, user_id) a composite index if useful for lookups:
            $table->index(['tenant_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // users
        Schema::table('users', function (Blueprint $table) {
            // if you added the composite unique above, drop it first:
            $table->dropUnique('users_tenant_id_email_unique');
            $table->dropColumn('tenant_id');
        });

        // password_reset_tokens
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // If you changed the primary to composite, revert it before dropping the column:
            $table->dropPrimary(['tenant_id', 'email']);
            $table->primary('email');
            $table->dropColumn('tenant_id');
        });

        // sessions
        Schema::table('sessions', function (Blueprint $table) {
            // if you created extra indices above, drop them first:
            $table->dropIndex(['tenant_id', 'user_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
