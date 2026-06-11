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
        // 1. ROLES
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nama_role', 50);
            });

            // Insert default roles
            DB::table('roles')->insert([
                ['id' => 1, 'nama_role' => 'Superadmin'],
                ['id' => 2, 'nama_role' => 'Admin Komunitas'],
                ['id' => 3, 'nama_role' => 'User'],
            ]);
        }

        // 2. USERS (Modify existing or create new)
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('username', 50)->unique();
                $table->string('email', 100)->unique();
                $table->string('password');
                $table->integer('role_id')->default(3);
                $table->string('foto_profil')->nullable();
                $table->text('bio')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->string('remember_token', 100)->nullable();
                $table->foreign('role_id')->references('id')->on('roles');
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'username')) {
                    $table->string('username', 50)->unique()->after('id');
                    // if they had 'name' from default laravel, we don't drop it here to avoid data loss, just add username
                }
                if (!Schema::hasColumn('users', 'role_id')) {
                    $table->integer('role_id')->default(3)->after('password');
                }
                if (!Schema::hasColumn('users', 'foto_profil')) {
                    $table->string('foto_profil')->nullable()->after('role_id');
                }
                if (!Schema::hasColumn('users', 'bio')) {
                    $table->text('bio')->nullable()->after('foto_profil');
                }
            });
        }

        // 3. RAK BUKU USER
        if (!Schema::hasTable('rak_buku_user')) {
            Schema::create('rak_buku_user', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->unsignedBigInteger('buku_id'); // buku table uses id() which is unsignedBigInteger
                $table->enum('status', ['sedang_dibaca', 'selesai', 'wishlist']);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->unique(['user_id', 'buku_id'], 'unique_user_buku_rak');
            });
        }

        // 4. RATING BUKU
        if (!Schema::hasTable('rating_buku')) {
            Schema::create('rating_buku', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->unsignedBigInteger('buku_id');
                $table->integer('skor_rating');
                $table->text('ulasan_teks')->nullable();
                $table->dateTime('tanggal_rating')->useCurrent();
                $table->unique(['user_id', 'buku_id'], 'unique_user_buku_rating');
                $table->index(['buku_id', 'skor_rating'], 'idx_buku_rating');
            });
        }

        // 5. KOMUNITAS
        if (!Schema::hasTable('komunitas')) {
            Schema::create('komunitas', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nama_komunitas', 100)->unique();
                $table->text('deskripsi');
                $table->text('peraturan')->nullable();
                $table->string('banner_komunitas');
                $table->integer('creator_id')->nullable();
                $table->enum('status', ['pending', 'aktif', 'nonaktif'])->default('pending');
                $table->text('catatan_penolakan')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index('status', 'idx_status');
            });
        }

        // 6. ANGGOTA KOMUNITAS
        if (!Schema::hasTable('anggota_komunitas')) {
            Schema::create('anggota_komunitas', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->integer('komunitas_id');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->dateTime('tanggal_bergabung')->useCurrent();
                $table->unique(['user_id', 'komunitas_id'], 'unique_user_komunitas');
            });
        }

        // 7. POSTINGAN KOMUNITAS
        if (!Schema::hasTable('postingan_komunitas')) {
            Schema::create('postingan_komunitas', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('komunitas_id');
                $table->integer('user_id');
                $table->string('judul')->nullable();
                $table->text('konten');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 8. KOMENTAR POSTINGAN
        if (!Schema::hasTable('komentar_postingan')) {
            Schema::create('komentar_postingan', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('postingan_id');
                $table->integer('user_id');
                $table->text('konten');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 9. PRELOVED BOOKS
        if (!Schema::hasTable('preloved_books')) {
            Schema::create('preloved_books', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->string('judul_buku');
                $table->string('kondisi_buku', 100);
                $table->decimal('harga', 10, 2)->default(0.00);
                $table->string('foto_buku');
                $table->text('deskripsi');
                $table->string('no_wa', 20);
                $table->enum('status_buku', ['tersedia', 'terjual', 'ditangguhkan'])->default('tersedia');
                $table->timestamp('created_at')->useCurrent();
                $table->index('status_buku', 'idx_status');
            });
        }

        // 10. LOG PERILAKU USER
        if (!Schema::hasTable('log_perilaku_user')) {
            Schema::create('log_perilaku_user', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->enum('tipe_aktivitas', ['cari', 'klik_detail', 'tambah_wishlist', 'beri_rating_tinggi', 'preloved']);
                $table->integer('bobot');
                $table->string('metadata')->nullable();
                $table->string('genre_terkait', 50)->nullable();
                $table->dateTime('tanggal_aktivitas')->useCurrent();
                $table->index(['user_id', 'tanggal_aktivitas'], 'idx_user_activity');
                $table->index('genre_terkait', 'idx_genre');
            });
        }

        // 11. AGREGASI REKOMENDASI
        if (!Schema::hasTable('agregasi_rekomendasi')) {
            Schema::create('agregasi_rekomendasi', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->string('genre', 50);
                $table->integer('total_skor')->default(0);
                $table->date('periode_mulai');
                $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();
                $table->unique(['user_id', 'genre'], 'unique_user_genre');
                $table->index(['user_id', 'total_skor'], 'idx_user_score');
            });
        }

        // 12. NOTIFIKASI
        if (!Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->string('tipe', 50);
                $table->text('pesan');
                $table->string('url_target')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('created_at')->useCurrent();
                $table->index(['user_id', 'is_read'], 'idx_user_unread');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop users or roles because they might contain data
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('agregasi_rekomendasi');
        Schema::dropIfExists('log_perilaku_user');
        Schema::dropIfExists('preloved_books');
        // Schema::dropIfExists('komentar_postingan');
        // Schema::dropIfExists('postingan_komunitas');
        Schema::dropIfExists('anggota_komunitas');
        Schema::dropIfExists('komunitas');
        Schema::dropIfExists('rating_buku');
        Schema::dropIfExists('rak_buku_user');
    }
};
