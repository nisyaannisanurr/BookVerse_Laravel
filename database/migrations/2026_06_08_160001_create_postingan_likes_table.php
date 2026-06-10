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
        Schema::create('postingan_likes', function (Blueprint $table) {
            $table->id();
            $table->integer('postingan_id');
            $table->integer('user_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('postingan_id')->references('id')->on('postingan_komunitas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Prevent duplicate likes
            $table->unique(['postingan_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postingan_likes');
    }
};
