@extends('layouts.main')
@section('title', 'Buat Kampanye Donasi — BookVerse')
@section('content')

<div style="max-width: 620px; margin: 0 auto;">

    <div style="margin-bottom: 16px;" class="fade-in-up">
        <a href="{{ url('/donasi/dashboard') }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">← Kembali ke Dashboard</a>
    </div>

    <div style="text-align: center; margin-bottom: 28px;" class="fade-in-up">
        <span style="font-size: 3.5rem; display: block; margin-bottom: 8px;">📢</span>
        <h1 style="font-family: var(--font-display); font-size: 1.8rem; font-weight: 800; color: var(--text); margin-bottom: 8px;">
            Buat Kampanye Donasi
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Beritahu komunitas BookVerse tentang kebutuhan buku di instansi Anda.
        </p>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 24px; padding: 36px; margin-bottom: 32px;" class="fade-in-up">
        <form method="POST" action="{{ url('/donasi/campaign/create') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                    Judul Kampanye <span style="color: var(--danger);">*</span>
                </label>
                <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Butuh 100 Buku Cerita untuk Anak SD" required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                    Deskripsi <span style="color: var(--danger);">*</span>
                </label>
                <textarea name="deskripsi" rows="5" placeholder="Jelaskan secara detail apa yang dibutuhkan, untuk siapa, dan mengapa kampanye ini penting..." required
                          style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none; resize: vertical;">{{ old('deskripsi') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                        Target Buku <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" name="target_buku" value="{{ old('target_buku', 50) }}" min="1" max="10000" required
                           style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Berapa buku yang dibutuhkan</div>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                        Batas Waktu <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="date" name="batas_waktu" value="{{ old('batas_waktu') }}" min="{{ date('Y-m-d', strtotime('+7 days')) }}" required
                           style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Minimal 7 hari dari sekarang</div>
                </div>
            </div>

            <div style="margin-bottom: 28px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                    Foto Kampanye (Opsional)
                </label>
                <input type="file" name="foto_campaign" accept="image/*"
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text);">
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Foto yang menggambarkan kebutuhan buku (JPG/PNG/WEBP, maks 5MB)</div>
            </div>

            <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 14px; padding: 16px; margin-bottom: 24px;">
                <div style="font-size: 0.85rem; color: #1e40af; line-height: 1.6;">
                    ℹ️ Kampanye akan ditinjau terlebih dahulu oleh admin sebelum ditampilkan ke publik. Proses persetujuan memakan waktu 1-2 hari kerja.
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; border-radius: 50px; font-weight: 700; font-size: 1.05rem; background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 8px 20px rgba(217, 119, 6, 0.3);">
                📤 Ajukan Kampanye
            </button>
        </form>
    </div>
</div>

@endsection
