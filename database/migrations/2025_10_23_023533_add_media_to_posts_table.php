<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Use ENUM for MySQL; default to 'image' (adjust as needed)
            $table->enum('media_type', ['image', 'video'])
                  ->default('image')
                  ->after('body'); // move as you like
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('media_type');
        });
    }
};
