<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix collation mismatch: align buku.genre_buku and genres.nama_genre
     * to utf8mb4_unicode_ci so they can be compared in SQL joins/subqueries.
     */
    public function up(): void
    {
        // Fix buku.genre_buku collation → utf8mb4_unicode_ci
        DB::statement("ALTER TABLE `buku` MODIFY `genre_buku` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL");

        // Fix genres.nama_genre collation → utf8mb4_unicode_ci (ensures consistency)
        DB::statement("ALTER TABLE `genres` MODIFY `nama_genre` VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }

    public function down(): void
    {
        // Revert buku.genre_buku back to general_ci
        DB::statement("ALTER TABLE `buku` MODIFY `genre_buku` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL");
    }
};
