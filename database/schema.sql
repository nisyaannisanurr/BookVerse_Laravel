-- ============================================
-- BookVerse Database Schema
-- MySQL 8.x
-- ============================================

CREATE DATABASE IF NOT EXISTS bookverse 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE bookverse;

-- ============================================
-- ROLES
-- ============================================
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

INSERT INTO roles (id, nama_role) VALUES 
    (1, 'Superadmin'),
    (2, 'Admin Komunitas'),
    (3, 'User');

-- ============================================
-- USERS
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 3,
    foto_profil VARCHAR(255) NULL,
    bio TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- Superadmin seed (password: SuperAdmin123!)
INSERT INTO users (username, email, password, role_id, bio) VALUES 
    ('superadmin', 'admin@bookverse.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Administrator BookVerse');

-- ============================================
-- BUKU (Master Book Catalog)
-- ============================================
CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(150) NOT NULL,
    sinopsis TEXT NOT NULL,
    cover_buku VARCHAR(255) NOT NULL,
    genre_buku VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample books
INSERT INTO buku (judul, penulis, sinopsis, cover_buku, genre_buku) VALUES
    ('Laskar Pelangi', 'Andrea Hirata', 'Kisah inspiratif tentang 10 anak dari keluarga miskin di Belitung yang berjuang mengejar pendidikan. Melalui keberanian dan tekad yang kuat, mereka membuktikan bahwa kemiskinan bukanlah penghalang untuk meraih mimpi. Sebuah novel yang menyentuh hati dan memberikan motivasi bagi siapa saja yang membacanya.', 'laskar_pelangi.jpg', 'Fiksi'),
    ('Bumi Manusia', 'Pramoedya Ananta Toer', 'Novel pertama dari Tetralogi Buru yang mengisahkan kehidupan Minke, seorang pribumi terpelajar di era kolonial Belanda. Melalui kisah cintanya dengan Annelies, Minke menghadapi diskriminasi rasial dan ketidakadilan sistem kolonial. Sebuah karya sastra monumental yang menggugah kesadaran nasional.', 'bumi_manusia.jpg', 'Sejarah'),
    ('Filosofi Teras', 'Henry Manampiring', 'Buku yang memperkenalkan filsafat Stoisisme dan menerapkannya dalam kehidupan sehari-hari manusia modern Indonesia. Dengan gaya bahasa yang ringan dan penuh humor, buku ini memberikan panduan praktis untuk mengelola emosi, menghadapi masalah, dan menemukan kebahagiaan sejati.', 'filosofi_teras.jpg', 'Sains'),
    ('Pulang', 'Tere Liye', 'Novel tentang Bujang, seorang anak yatim yang tumbuh menjadi ahli keuangan hebat di dunia gelap. Dari pedalaman Sumatera hingga kota-kota besar dunia, kisah ini penuh dengan konflik batin antara kesetiaan dan ambisi, cinta dan pengkhianatan.', 'pulang.jpg', 'Fiksi'),
    ('Cantik Itu Luka', 'Eka Kurniawan', 'Sebuah novel epik yang mengisahkan kehidupan Dewi Ayu, seorang perempuan cantik yang dikutuk, dan keempat putrinya di sebuah kota pesisir fiktif. Cerita ini membentang dari era kolonial Belanda hingga reformasi, menggabungkan realisme magis dengan sejarah Indonesia.', 'cantik_itu_luka.jpg', 'Fiksi'),
    ('Sapiens: Riwayat Singkat Umat Manusia', 'Yuval Noah Harari', 'Sebuah perjalanan menakjubkan melalui sejarah umat manusia dari zaman batu hingga era digital. Harari menjelaskan bagaimana Homo sapiens berhasil mendominasi planet ini melalui revolusi kognitif, pertanian, dan sains. Buku yang mengubah cara kita memandang sejarah.', 'sapiens.jpg', 'Sains'),
    ('Dilan 1990', 'Pidi Baiq', 'Kisah cinta remaja yang manis dan penuh kejutan antara Milea dan Dilan di kota Bandung tahun 1990. Dengan karakter Dilan yang unik dan romantis, novel ini berhasil mencuri hati jutaan pembaca Indonesia. Sebuah cerita yang membangkitkan nostalgia masa SMA.', 'dilan.jpg', 'Romance'),
    ('Laut Bercerita', 'Leila S. Chudori', 'Novel yang mengangkat tragedi penculikan aktivis tahun 1998. Melalui sudut pandang Biru Laut, seorang aktivis mahasiswa, dan Asmara, adiknya yang mencari kebenaran, novel ini mengungkap luka mendalam bangsa Indonesia yang belum tersembuhkan.', 'laut_bercerita.jpg', 'Sejarah'),
    ('The Midnight Library', 'Matt Haig', 'Kisah Nora Seed yang menemukan perpustakaan ajaib di antara hidup dan mati, di mana setiap buku menawarkan kehidupan alternatif yang bisa ia jalani. Sebuah novel yang mengharukan tentang penyesalan, pilihan hidup, dan menemukan makna kebahagiaan.', 'midnight_library.jpg', 'Fiksi'),
    ('Hujan', 'Tere Liye', 'Novel tentang Lail dan Esok yang bertemu saat bencana alam dan tumbuh bersama menghadapi tantangan hidup. Di masa depan yang penuh teknologi canggih, mereka harus memilih antara menghapus kenangan menyakitkan atau menerimanya sebagai bagian dari kehidupan.', 'hujan.jpg', 'Romance'),
    ('Misteri Rumah Tua', 'Adi Surahman', 'Sekelompok remaja nekat menjelajahi rumah tua angker di pinggir kota yang telah ditinggalkan selama puluhan tahun. Mereka menemukan rahasia gelap tentang pemilik rumah yang menghilang secara misterius. Satu per satu, mereka mulai mengalami kejadian mengerikan.', 'misteri_rumah.jpg', 'Horor'),
    ('Jejak Langkah', 'Pramoedya Ananta Toer', 'Novel ketiga dari Tetralogi Buru yang melanjutkan perjalanan Minke sebagai jurnalis dan aktivis pergerakan nasional. Dalam perjuangannya melawan penjajahan, ia menghadapi pengkhianatan dan pengasingan, namun tetap berpegang pada cita-cita kemerdekaan.', 'jejak_langkah.jpg', 'Sejarah');

-- ============================================
-- RAK BUKU USER (User Bookshelf)
-- ============================================
CREATE TABLE rak_buku_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    status ENUM('sedang_dibaca','selesai','wishlist') NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_buku_rak (user_id, buku_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- RATING BUKU
-- ============================================
CREATE TABLE rating_buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    skor_rating INT NOT NULL CHECK (skor_rating BETWEEN 1 AND 5),
    ulasan_teks TEXT NULL,
    tanggal_rating DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_buku_rating (user_id, buku_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- KOMUNITAS
-- ============================================
CREATE TABLE komunitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_komunitas VARCHAR(100) UNIQUE NOT NULL,
    deskripsi TEXT NOT NULL,
    peraturan TEXT NULL,
    banner_komunitas VARCHAR(255) NOT NULL,
    creator_id INT NULL,
    status ENUM('pending','aktif','nonaktif') DEFAULT 'pending',
    catatan_penolakan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- ANGGOTA KOMUNITAS
-- ============================================
CREATE TABLE anggota_komunitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    komunitas_id INT NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    tanggal_bergabung DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_komunitas (user_id, komunitas_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (komunitas_id) REFERENCES komunitas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- POSTINGAN KOMUNITAS
-- ============================================
CREATE TABLE postingan_komunitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    komunitas_id INT NOT NULL,
    user_id INT NOT NULL,
    judul VARCHAR(255) NULL,
    konten TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (komunitas_id) REFERENCES komunitas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- KOMENTAR POSTINGAN
-- ============================================
CREATE TABLE komentar_postingan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    postingan_id INT NOT NULL,
    user_id INT NOT NULL,
    konten TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postingan_id) REFERENCES postingan_komunitas(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- PRELOVED BOOKS
-- ============================================
CREATE TABLE preloved_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul_buku VARCHAR(255) NOT NULL,
    kondisi_buku VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) DEFAULT 0.00,
    foto_buku VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    no_wa VARCHAR(20) NOT NULL,
    status_buku ENUM('tersedia','terjual','ditangguhkan') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- LOG PERILAKU USER (Behavior Logging)
-- ============================================
CREATE TABLE log_perilaku_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipe_aktivitas ENUM('cari','klik_detail','tambah_wishlist','beri_rating_tinggi') NOT NULL,
    bobot INT NOT NULL,
    metadata VARCHAR(255) NULL,
    genre_terkait VARCHAR(50) NULL,
    tanggal_aktivitas DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- AGREGASI REKOMENDASI
-- ============================================
CREATE TABLE agregasi_rekomendasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    genre VARCHAR(50) NOT NULL,
    total_skor INT NOT NULL DEFAULT 0,
    periode_mulai DATE NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_genre (user_id, genre),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- NOTIFIKASI
-- ============================================
CREATE TABLE notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipe VARCHAR(50) NOT NULL,
    pesan TEXT NOT NULL,
    url_target VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- INDEXES for performance
-- ============================================
ALTER TABLE log_perilaku_user ADD INDEX idx_user_activity (user_id, tanggal_aktivitas);
ALTER TABLE log_perilaku_user ADD INDEX idx_genre (genre_terkait);
ALTER TABLE agregasi_rekomendasi ADD INDEX idx_user_score (user_id, total_skor DESC);
ALTER TABLE rating_buku ADD INDEX idx_buku_rating (buku_id, skor_rating);
ALTER TABLE notifikasi ADD INDEX idx_user_unread (user_id, is_read);
ALTER TABLE buku ADD INDEX idx_genre (genre_buku);
ALTER TABLE preloved_books ADD INDEX idx_status (status_buku);
ALTER TABLE komunitas ADD INDEX idx_status (status);

