<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        /**
         * USERS: add tenant_id and enforce (tenant_id, email) uniqueness.
         * If a previous unique index existed on `email`, drop it first.
         */
        Schema::table('users', function (Blueprint $table) {
            // 1) Add tenant_id (nullable for landlord/global) if missing
            if (! Schema::hasColumn('users', 'tenant_id')) {
                $table->string('tenant_id')
                    ->nullable()
                    ->default(null)
                    ->index()
                    ->after('id');
            }

            // 2) Drop legacy unique on email if it exists (index name is usually users_email_unique)
            //    If you never had a unique on email, this will be a no-op on most drivers.
            try {
                $table->dropUnique('users_email_unique');
            } catch (\Throwable $e) {
                // ignore if it didn't exist
            }

            // 3) Add composite unique on (tenant_id, email)
            //    Note: In MySQL, multiple NULLs in a UNIQUE composite are allowed per row,
            //    but combined with email it still enforces uniqueness per tenant effectively.
            $table->unique(['tenant_id', 'email'], 'users_tenant_email_unique');
        });

        /**
         * PASSWORD RESET TOKENS:
         * Add tenant_id and switch primary key from (email) to (tenant_id, email)
         */
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('password_reset_tokens', 'tenant_id')) {
                $table->string('tenant_id')
                    ->nullable()
                    ->default(null)
                    ->index()
                    ->after('email');
            }

            // Drop the existing PK on email (Laravel default name)
            try {
                $table->dropPrimary('password_reset_tokens_email_primary');
            } catch (\Throwable $e) {
                // ignore if it's already changed
            }

            // Create composite primary key
            $table->primary(['tenant_id', 'email'], 'password_reset_tokens_tenant_email_primary');
        });

        /**
         * SESSIONS:
         * Add tenant_id and helpful composite index for queries
         */
        Schema::table('sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('sessions', 'tenant_id')) {
                $table->string('tenant_id')
                    ->nullable()
                    ->default(null)
                    ->index()
                    ->after('id');
            }

            // Optional composite index for faster lookups by tenant+user
            $table->index(['tenant_id', 'user_id'], 'sessions_tenant_user_index');
        });
    }

    public function down(): void
    {
        /**
         * USERS: revert to legacy unique(email) and drop composite unique + tenant_id column (optional)
         */
        Schema::table('users', function (Blueprint $table) {
            // Drop composite unique if present
            try {
                $table->dropUnique('users_tenant_email_unique');
            } catch (\Throwable $e) {
                // ignore if not present
            }

            // Restore unique on email (legacy behavior)
            // If you don't want to restore this, you can remove this line.
            $table->unique('email', 'users_email_unique');

            // Optionally drop tenant_id (comment out if you want to keep it)
            // Be careful: if other code depends on tenant_id, don't drop it.
            // try {
            //     $table->dropColumn('tenant_id');
            // } catch (\Throwable $e) { /* ignore */ }
        });

        /**
         * PASSWORD RESET TOKENS: revert PK to (email), drop composite PK
         */
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Drop composite PK if present
            try {
                $table->dropPrimary('password_reset_tokens_tenant_email_primary');
            } catch (\Throwable $e) {
                // ignore
            }

            // Restore original PK on email
            $table->primary('email', 'password_reset_tokens_email_primary');

            // Optionally drop tenant_id
            // try {
            //     $table->dropColumn('tenant_id');
            // } catch (\Throwable $e) { /* ignore */ }
        });

        /**
         * SESSIONS: drop helper index and (optionally) tenant_id
         */
        Schema::table('sessions', function (Blueprint $table) {
            try {
                $table->dropIndex('sessions_tenant_user_index');
            } catch (\Throwable $e) {
                // ignore
            }

            // Optionally drop tenant_id
            // try {
            //     $table->dropColumn('tenant_id');
            // } catch (\Throwable $e) { /* ignore */ }
        });
    }
};


