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
        Schema::table('donasi_bukus', function (Blueprint $table) {
            $table->string('foto_terima')->nullable()->after('resi_pengiriman');
            $table->text('pesan_terima')->nullable()->after('foto_terima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donasi_bukus', function (Blueprint $table) {
            $table->dropColumn(['foto_terima', 'pesan_terima']);
        });
    }
};
