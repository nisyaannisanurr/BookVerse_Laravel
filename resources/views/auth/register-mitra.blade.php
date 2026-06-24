<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mitra — BookVerse</title>
    <meta name="description" content="Daftarkan sekolah, panti asuhan, atau taman bacaan Anda untuk menerima donasi buku.">
    <link rel="icon" href="{{ asset('images/logo-bookverse.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <style>
        .login-hero {
            background: linear-gradient(135deg, #ede9fb 0%, #f5f0f0 60%, #fdf3e3 100%);
            padding: var(--space-2xl) 0;
            margin-bottom: 0;
            border-bottom: 1px solid var(--border);
            min-height: 100vh;
        }
        .mitra-register-wrap {
            max-width: 720px;
            margin: 0 auto;
            padding: 0 var(--space-xl);
        }
        .mitra-form-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 36px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
        }
        .mitra-form-card h2 {
            font-family: var(--font-display);
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }
        .mitra-form-card .section-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 24px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.88rem;
            margin-bottom: 6px;
            color: var(--text);
        }
        .form-group label .required { color: var(--danger); }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 0.95rem;
            background: var(--bg-surface);
            color: var(--text);
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12);
            background: var(--bg-white);
        }
        .form-group .file-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .syarat-list {
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }
        .syarat-list h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 12px;
        }
        .syarat-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .syarat-list li {
            font-size: 0.85rem;
            color: #78716c;
            padding: 6px 0;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .syarat-list li::before {
            content: '✅';
            font-size: 0.8rem;
            flex-shrink: 0;
            margin-top: 1px;
        }
        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
            .mitra-form-card { padding: 24px 18px; }
        }
    </style>
</head>
<body>

<nav class="navbar" id="mainNavbar">
    <div class="navbar-inner">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('images/logo-bookverse.png') }}" alt="BookVerse" class="logo-img">
            <div class="logo-text">
                <span class="logo-name">Book<span>Verse</span></span>
                <span class="logo-tagline">Buka Buku, Buka Dunia.</span>
            </div>
        </a>
        <div class="nav-actions" style="margin-left:auto; display: flex; gap: 8px; align-items: center;">
            @auth
                <span style="font-size: 0.85rem; font-weight: 600; color: var(--text);">Hai, {{ auth()->user()->username }}</span>
                <a href="{{ url('/logout') }}" class="btn btn-outline btn-sm">Keluar</a>
            @else
                <a href="{{ url('/login') }}" class="btn btn-outline btn-sm">Masuk</a>
            @endauth
        </div>
    </div>
</nav>

<div class="page-wrapper">
<section class="login-hero">
<div class="mitra-register-wrap">

    {{-- ─── HEADER ─────────────────────────── --}}
    <div style="text-align: center; margin-bottom: 32px;" class="fade-in-up">
        <span style="font-size: 4rem; display: block; margin-bottom: 10px;">🤝</span>
        <h1 style="font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: var(--text); margin-bottom: 8px;">
            Daftar Sebagai Mitra
        </h1>
        <p style="color: var(--text-muted); font-size: 1rem; max-width: 480px; margin: 0 auto;">
            Daftarkan sekolah, panti asuhan, atau taman bacaan Anda untuk menerima donasi buku dari komunitas BookVerse.
        </p>
    </div>

    {{-- ─── SYARAT ─────────────────────────── --}}
    <div class="syarat-list fade-in-up">
        <h3>📋 Syarat Pendaftaran Mitra</h3>
        <ul>
            <li>Foto KTP Penanggung Jawab (Kepala Sekolah, Ketua Yayasan, atau Pengurus TBM)</li>
            <li>Surat Keterangan/Legalitas: NPSN (Sekolah), Akta Notaris (Yayasan), atau SK RT/RW (TBM)</li>
            <li>Foto bangunan/gedung instansi dari luar</li>
            <li>Foto kegiatan membaca/belajar yang sedang berlangsung</li>
            <li>Link Google Maps lokasi instansi (opsional)</li>
        </ul>
    </div>

    {{-- ─── FORM ───────────────────────────── --}}
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 24px;">❌ {!! session('error') !!}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">✅ {{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ url('/mitra/daftar') }}" enctype="multipart/form-data">
        @csrf

        {{-- Akun --}}
        <div class="mitra-form-card fade-in-up">
            <h2>👤 Data Akun</h2>
            <p class="section-desc">Buat akun login untuk instansi Anda.</p>

            <div class="form-row">
                <div class="form-group">
                    <label>Username <span class="required">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Contoh: sd_negeri_1" required>
                </div>
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@instansi.com" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" placeholder="Min. 8 karakter (A-Z, a-z, 0-9)" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password">
                </div>
            </div>
        </div>

        {{-- Data Instansi --}}
        <div class="mitra-form-card fade-in-up">
            <h2>🏫 Data Instansi</h2>
            <p class="section-desc">Informasi lengkap tentang sekolah, panti asuhan, atau taman bacaan Anda.</p>

            <div class="form-row">
                <div class="form-group">
                    <label>Nama Instansi <span class="required">*</span></label>
                    <input type="text" name="nama_instansi" value="{{ old('nama_instansi') }}" placeholder="SD Negeri 1 Contoh" required>
                </div>
                <div class="form-group">
                    <label>Kategori <span class="required">*</span></label>
                    <select name="kategori" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="Sekolah" {{ old('kategori') === 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
                        <option value="Panti Asuhan" {{ old('kategori') === 'Panti Asuhan' ? 'selected' : '' }}>Panti Asuhan</option>
                        <option value="Taman Bacaan" {{ old('kategori') === 'Taman Bacaan' ? 'selected' : '' }}>Taman Bacaan</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Instansi Daerah / Desa <span class="required">*</span></label>
                <select name="instansi_daerah_id" required>
                    <option value="" disabled selected>-- Pilih Instansi Daerah/Desa --</option>
                    @foreach($daerahs as $d)
                        <option value="{{ $d->id }}" {{ old('instansi_daerah_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_daerah }}</option>
                    @endforeach
                </select>
                <div class="file-hint">Pilih desa tempat instansi Anda berada</div>
            </div>

            <div class="form-group">
                <label>Alamat Lengkap <span class="required">*</span></label>
                <textarea name="alamat_lengkap" rows="3" placeholder="Jl. Contoh No. 123, Desa/Kelurahan, Kecamatan, Kabupaten/Kota, Provinsi" required>{{ old('alamat_lengkap') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nama Penanggung Jawab <span class="required">*</span></label>
                    <input type="text" name="nama_penanggung_jawab" value="{{ old('nama_penanggung_jawab') }}" placeholder="Nama lengkap" required>
                </div>
                <div class="form-group">
                    <label>No. Telepon/WA <span class="required">*</span></label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" placeholder="08xxxxxxxxxx" required>
                </div>
            </div>

            <div class="form-group">
                <label>Link Google Maps (opsional)</label>
                <input type="url" name="link_maps" value="{{ old('link_maps') }}" placeholder="https://maps.google.com/...">
            </div>
        </div>

        {{-- Dokumen --}}
        <div class="mitra-form-card fade-in-up">
            <h2>📄 Dokumen Verifikasi</h2>
            <p class="section-desc">Unggah dokumen berikut agar akun Anda bisa diverifikasi oleh admin. Format: JPG, PNG, WEBP (maks. 5MB).</p>

            <div class="form-row">
                <div class="form-group">
                    <label>Foto KTP Penanggung Jawab <span class="required">*</span></label>
                    <input type="file" name="file_ktp" accept="image/*" required>
                    <div class="file-hint">Foto KTP harus jelas dan terbaca</div>
                </div>
                <div class="form-group">
                    <label>Surat Keterangan/Legalitas <span class="required">*</span></label>
                    <input type="file" name="file_legalitas" accept="image/*" required>
                    <div class="file-hint">NPSN / Akta Notaris / SK RT/RW</div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Foto Bangunan Instansi <span class="required">*</span></label>
                    <input type="file" name="foto_bangunan" accept="image/*" required>
                    <div class="file-hint">Foto tampak depan bangunan</div>
                </div>
                <div class="form-group">
                    <label>Foto Kegiatan <span class="required">*</span></label>
                    <input type="file" name="foto_kegiatan" accept="image/*" required>
                    <div class="file-hint">Foto anak-anak/masyarakat saat belajar atau membaca</div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div style="text-align: center; margin-bottom: 40px;" class="fade-in-up">
            <button type="submit" class="btn btn-primary" style="padding: 16px 48px; border-radius: 50px; font-weight: 700; font-size: 1.1rem; background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 8px 25px rgba(217, 119, 6, 0.3);">
                📨 Kirim Pendaftaran
            </button>
            <p style="margin-top: 18px; font-size: 0.9rem; color: var(--text-muted);">
                @auth
                    Sudah mendaftar sebagai mitra? <a href="{{ url('/') }}" style="color: #f59e0b; font-weight: 700; text-decoration: none;">Kembali ke Beranda</a>
                @else
                    Sudah mendaftar sebagai mitra? <a href="{{ url('/mitra/masuk') }}" style="color: #f59e0b; font-weight: 700; text-decoration: none;">Masuk di sini</a>
                @endauth
            </p>
            <p style="margin-top: 8px; font-size: 0.82rem; color: var(--text-muted);">
                Akun baru akan diverifikasi oleh admin dalam 1-3 hari kerja.
            </p>
        </div>
    </form>

</div>
</section>
</div>
</body>
</html>
