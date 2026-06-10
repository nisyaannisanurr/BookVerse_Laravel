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
        Schema::create('tantangan_membacas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('komunitas_id');
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->integer('target_buku')->default(1);
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->foreign('komunitas_id')->references('id')->on('komunitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tantangan_membacas');
    }
};
