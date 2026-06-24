<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert role Mitra if not exists
        if (!DB::table('roles')->where('id', 4)->exists()) {
            DB::table('roles')->insert([
                'id' => 4,
                'nama_role' => 'Mitra',
            ]);
        }

        // 1. MITRA VERIFICATIONS
        if (!Schema::hasTable('mitra_verifications')) {
            Schema::create('mitra_verifications', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->string('nama_instansi');
                $table->enum('kategori', ['Sekolah', 'Panti Asuhan', 'Taman Bacaan']);
                $table->text('alamat_lengkap');
                $table->string('link_maps')->nullable();
                $table->string('nama_penanggung_jawab');
                $table->string('no_telepon', 20);
                $table->string('file_ktp');
                $table->string('file_legalitas');
                $table->string('foto_bangunan');
                $table->string('foto_kegiatan');
                $table->enum('status_verifikasi', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('catatan_admin')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->index('user_id');
                $table->index('status_verifikasi');
            });
        }

        // 2. CAMPAIGN DONASI
        if (!Schema::hasTable('campaign_donasis')) {
            Schema::create('campaign_donasis', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id'); // mitra
                $table->string('judul');
                $table->text('deskripsi');
                $table->string('foto_campaign')->nullable();
                $table->integer('target_buku')->default(0);
                $table->integer('terkumpul')->default(0);
                $table->date('batas_waktu');
                $table->enum('status', ['pending', 'active', 'completed', 'rejected'])->default('pending');
                $table->text('catatan_admin')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->index(['status', 'batas_waktu']);
                $table->index('user_id');
            });
        }

        // 3. DONASI BUKU
        if (!Schema::hasTable('donasi_bukus')) {
            Schema::create('donasi_bukus', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('campaign_id');
                $table->integer('user_id'); // donatur
                $table->string('judul_buku');
                $table->integer('jumlah')->default(1);
                $table->enum('kondisi', ['baru', 'bekas_layak'])->default('bekas_layak');
                $table->string('resi_pengiriman')->nullable();
                $table->text('catatan')->nullable();
                $table->enum('status_pengiriman', ['menunggu_dikirim', 'dikirim', 'diterima'])->default('menunggu_dikirim');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->index('campaign_id');
                $table->index('user_id');
                $table->index('status_pengiriman');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi_bukus');
        Schema::dropIfExists('campaign_donasis');
        Schema::dropIfExists('mitra_verifications');

        // Don't delete role to avoid data loss
    }
};
