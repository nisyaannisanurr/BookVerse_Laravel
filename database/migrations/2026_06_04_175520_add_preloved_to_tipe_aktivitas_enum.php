<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend ENUM to include klik_preloved
        DB::statement("ALTER TABLE `log_perilaku_user` MODIFY `tipe_aktivitas`
            ENUM('cari','klik_detail','tambah_wishlist','beri_rating_tinggi','klik_preloved')
            NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `log_perilaku_user` MODIFY `tipe_aktivitas`
            ENUM('cari','klik_detail','tambah_wishlist','beri_rating_tinggi')
            NOT NULL");
    }
};
