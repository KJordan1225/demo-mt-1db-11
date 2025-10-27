<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the existing unique constraint on the email column (if any)
            $table->dropUnique(['email']); 

            // Add a composite unique index on email and tenant_id columns
            $table->unique(['email', 'tenant_id']);
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
            // Drop the composite unique index
            $table->dropUnique(['email', 'tenant_id']);
            
            // Optionally, add back the original unique constraint if needed
            $table->unique('email');
        });
    }

};
