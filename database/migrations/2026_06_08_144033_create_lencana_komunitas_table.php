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
        Schema::create('lencana_komunitas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('komunitas_id');
            $table->string('nama_lencana', 100);
            $table->string('ikon', 50)->nullable(); // Emoji
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('komunitas_id')->references('id')->on('komunitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lencana_komunitas');
    }
};
