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
            $table->string('no_wa_donatur', 20)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donasi_bukus', function (Blueprint $table) {
            $table->dropColumn('no_wa_donatur');
        });
    }
};
