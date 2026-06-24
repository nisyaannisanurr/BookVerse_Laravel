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
            $table->unsignedInteger('tantangan_id')->nullable()->after('komunitas_id');
            $table->foreign('tantangan_id')->references('id')->on('tantangan_membacas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postingan_komunitas', function (Blueprint $table) {
            $table->dropForeign(['tantangan_id']);
            $table->dropColumn('tantangan_id');
        });
    }
};
