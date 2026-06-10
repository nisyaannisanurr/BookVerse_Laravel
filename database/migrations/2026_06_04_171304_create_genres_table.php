<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('nama_genre', 80)->unique();
            $table->string('deskripsi', 255)->nullable();
            $table->string('warna', 20)->nullable()->comment('Hex color for badge');
            $table->timestamps();
        });

        // Seed genres from existing buku data
        $existingGenres = DB::table('buku')
            ->select('genre_buku')
            ->distinct()
            ->whereNotNull('genre_buku')
            ->where('genre_buku', '!=', '')
            ->pluck('genre_buku');

        $colors = [
            '#4f3cc9', '#0284c7', '#16a34a', '#d97706',
            '#dc2626', '#7c3aed', '#0891b2', '#b45309',
        ];
        $i = 0;
        foreach ($existingGenres as $genre) {
            DB::table('genres')->insert([
                'nama_genre' => $genre,
                'warna'      => $colors[$i % count($colors)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $i++;
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};
