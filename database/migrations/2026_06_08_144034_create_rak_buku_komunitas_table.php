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
        Schema::create('rak_buku_komunitas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('komunitas_id');
            $table->integer('buku_id');
            $table->enum('tipe', ['wajib_baca', 'rekomendasi'])->default('rekomendasi');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('komunitas_id')->references('id')->on('komunitas')->onDelete('cascade');
            $table->foreign('buku_id')->references('id')->on('buku')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rak_buku_komunitas');
    }
};
