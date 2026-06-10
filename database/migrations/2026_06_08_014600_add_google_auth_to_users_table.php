<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth ID (null for non-Google users)
            $table->string('google_id')->nullable()->unique()->after('role_id');
            // Avatar URL from Google profile (null if not using Google or no photo)
            $table->string('avatar')->nullable()->after('google_id');
            // Password becomes nullable for Google-only users
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
