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
        Schema::create('pertanyaan_qnas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('qna_id');
            $table->integer('user_id');
            $table->text('pertanyaan');
            $table->text('jawaban')->nullable();
            $table->boolean('is_highlighted')->default(false);
            $table->timestamps();

            $table->foreign('qna_id')->references('id')->on('qna_komunitas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_qnas');
    }
};
