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
            $table->string('gambar')->nullable()->after('konten');
        });

        Schema::table('komentar_postingan', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('konten');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postingan_komunitas', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });

        Schema::table('komentar_postingan', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
