@extends('layouts.main')
@section('title', 'Ajukan Komunitas — BookVerse')
@section('content')

<div class="d-flex align-center gap-sm mb-lg">
    <a href="{{ url('/community') }}" class="btn btn-ghost btn-sm">← Kembali</a>
    <h2 style="font-family:var(--font-display);">➕ {{ auth()->user()->role_id <= 2 ? 'Buat' : 'Ajukan' }} Komunitas Baru</h2>
</div>

{{-- Alur untuk user biasa --}}
@if(auth()->user()->role_id === 3)
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-md);margin-bottom:var(--space-xl);">
    @foreach([
        ['1','📝','Isi Form','Lengkapi nama, deskripsi, peraturan, dan banner komunitas'],
        ['2','👑','Jadi Admin','Kamu otomatis menjadi Admin Komunitas setelah mengajukan'],
        ['3','✅','Tunggu Approval','Superadmin akan meninjau dan mengaktifkan komunitasmu'],
    ] as [$num, $icon, $title, $desc])
    <div class="card" style="text-align:center;padding:var(--space-lg);">
        <div style="width:36px;height:36px;background:var(--primary);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;margin:0 auto var(--space-sm);">{{ $num }}</div>
        <div style="font-size:1.4rem;margin-bottom:6px;">{{ $icon }}</div>
        <div style="font-weight:700;font-size:0.875rem;margin-bottom:4px;">{{ $title }}</div>
        <div style="font-size:0.78rem;color:var(--text-secondary);">{{ $desc }}</div>
    </div>
    @endforeach
</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ url('/community/create') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" for="nama_komunitas">Nama Komunitas</label>
                <input type="text" id="nama_komunitas" name="nama_komunitas" class="form-input" required maxlength="100" placeholder="Contoh: Pecinta Novel Fantasi">
            </div>

            <div class="form-group">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-textarea" required placeholder="Jelaskan komunitas Anda..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="peraturan">Peraturan (Opsional)</label>
                <textarea id="peraturan" name="peraturan" class="form-textarea" placeholder="Tulis peraturan komunitas..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Banner Komunitas</label>
                <div class="file-input-wrapper w-full">
                    <input type="file" name="banner" accept="image/jpeg,image/png,image/webp" required data-preview="bannerPreview">
                    <div class="file-input-label">
                        📷 Pilih gambar banner (JPG/PNG/WEBP, maks 5MB)
                    </div>
                </div>
                <img id="bannerPreview" src="" alt="" style="display:none;max-height:200px;border-radius:var(--radius-md);margin-top:var(--space-sm);">
            </div>

            <div class="alert alert-info">
                <span>ℹ️</span>
                <span>Komunitas akan ditinjau oleh Superadmin sebelum aktif. Anda akan mendapat notifikasi setelah disetujui.</span>
            </div>

            <div class="d-flex gap-sm mt-md">
                <button type="submit" class="btn btn-primary">🚀 Ajukan Komunitas</button>
                <a href="{{ url('/community') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
