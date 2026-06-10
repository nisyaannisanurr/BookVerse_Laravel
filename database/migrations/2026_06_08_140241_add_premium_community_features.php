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
        // 1. Tambah kolom di tabel komunitas (Sudah tertambah di run sebelumnya)
        // Schema::table('komunitas', function (Blueprint $table) {
        //     $table->string('tema_warna', 20)->default('#4f46e5')->after('banner_komunitas');
        //     $table->string('logo_komunitas')->nullable()->after('tema_warna');
        // });

        // 2. Tambah kolom di postingan_komunitas (Sudah tertambah)
        // Schema::table('postingan_komunitas', function (Blueprint $table) {
        //     $table->boolean('is_pinned')->default(false)->after('konten');
        // });

        // 3. Buat tabel laporan_komunitas
        Schema::create('laporan_komunitas', function (Blueprint $table) {
            $table->id();
            $table->integer('komunitas_id');
            $table->integer('pelapor_id');
            $table->integer('postingan_id')->nullable();
            $table->integer('komentar_id')->nullable();
            $table->text('alasan');
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending');
            $table->timestamps();

            // Foreign keys
            $table->foreign('komunitas_id')->references('id')->on('komunitas')->onDelete('cascade');
            $table->foreign('pelapor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('postingan_id')->references('id')->on('postingan_komunitas')->onDelete('cascade');
            $table->foreign('komentar_id')->references('id')->on('komentar_postingan')->onDelete('cascade');
        });

        // 4. Buat tabel event_komunitas
        Schema::create('event_komunitas', function (Blueprint $table) {
            $table->id();
            $table->integer('komunitas_id');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('tanggal_waktu');
            $table->string('lokasi')->nullable(); // Bisa URL Gmeet atau Zoom
            $table->timestamps();

            $table->foreign('komunitas_id')->references('id')->on('komunitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_komunitas');
        Schema::dropIfExists('laporan_komunitas');

        Schema::table('postingan_komunitas', function (Blueprint $table) {
            $table->dropColumn('is_pinned');
        });

        Schema::table('komunitas', function (Blueprint $table) {
            $table->dropColumn(['tema_warna', 'logo_komunitas']);
        });
    }
};
