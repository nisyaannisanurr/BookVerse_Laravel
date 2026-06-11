<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profanities', function (Blueprint $table) {
            $table->id();
            $table->string('kata')->unique();
            $table->timestamps();
        });

        // Insert initial bad words
        $badWords = [
            'anjing', 'babi', 'monyet', 'bangsat', 'tolol', 'goblok', 'bego', 
            'kampret', 'sialan', 'bajingan', 'jancok', 'asu', 'kontol', 'memek',
            'ngentot', 'perek', 'pelacur'
        ];

        $data = array_map(function ($word) {
            return ['kata' => $word, 'created_at' => now(), 'updated_at' => now()];
        }, $badWords);

        DB::table('profanities')->insert($data);
    }

    public function down(): void
    {
        Schema::dropIfExists('profanities');
    }
};
