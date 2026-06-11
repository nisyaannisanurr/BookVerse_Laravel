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
        if (!Schema::hasTable('buku')) {
            Schema::create('buku', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->string('penulis', 150);
                $table->text('sinopsis');
                $table->string('cover_buku');
                $table->string('genre_buku', 50)->index('idx_genre');
                $table->timestamp('created_at')->useCurrent();
                $table->softDeletes();
            });
        } else {
            Schema::table('buku', function (Blueprint $table) {
                if (!Schema::hasColumn('buku', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
