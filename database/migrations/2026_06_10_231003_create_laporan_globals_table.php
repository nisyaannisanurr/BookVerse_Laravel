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
        Schema::create('laporan_globals', function (Blueprint $table) {
            $table->id();
            $table->integer('pelapor_id');
            $table->foreign('pelapor_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('tipe_entitas'); // 'buku', 'preloved', 'user', 'komunitas'
            $table->unsignedBigInteger('entitas_id');
            $table->string('alasan');
            $table->text('detail_tambahan')->nullable();
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_globals');
    }
};
