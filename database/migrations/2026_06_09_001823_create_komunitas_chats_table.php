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
        Schema::create('komunitas_chats', function (Blueprint $table) {
            $table->id();
            $table->integer('komunitas_id');
            $table->integer('user_id');
            $table->unsignedBigInteger('parent_id')->nullable(); // For reply
            $table->text('pesan');
            $table->timestamps();

            $table->foreign('komunitas_id')->references('id')->on('komunitas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('komunitas_chats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komunitas_chats');
    }
};
