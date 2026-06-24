<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instansi_daerahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_daerah'); // Contoh: Bantan Air, Desa Deluk, dll
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Modifikasi tabel mitra_verifications
        Schema::table('mitra_verifications', function (Blueprint $table) {
            // Tambah instansi_daerah_id setelah user_id
            $table->unsignedBigInteger('instansi_daerah_id')->nullable()->after('user_id');
            
            // Opsional: hapus nama_instansi jika ingin instansi_daerah menggantikan fungsinya
            // Tapi karena instansi daerah itu "Desa", dan nama instansi itu "SDN 1 Bantan",
            // Keduanya harus dipertahankan. (Misal: Nama: SDN 1 Bantan, Daerah: Bantan Air).
        });
    }

    public function down(): void
    {
        Schema::table('mitra_verifications', function (Blueprint $table) {
            $table->dropColumn('instansi_daerah_id');
        });
        
        Schema::dropIfExists('instansi_daerahs');
    }
};
