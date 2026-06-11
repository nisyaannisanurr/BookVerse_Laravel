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
        Schema::table('postingan_komunitas', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('komentar_postingan', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postingan_komunitas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('komentar_postingan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
