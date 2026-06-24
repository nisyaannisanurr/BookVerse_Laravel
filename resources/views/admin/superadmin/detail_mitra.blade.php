@extends('layouts.admin')

@section('styles')
<style>
    .mitra-detail-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 20px 40px rgba(67, 56, 202, 0.2);
        position: relative;
        overflow: hidden;
    }
    .mitra-detail-header::after {
        content: ''; position: absolute; right: -50px; top: -50px;
        width: 300px; height: 300px; background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .mitra-badge {
        display: inline-block; padding: 6px 16px; border-radius: 50px;
        font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
        background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);
        margin-bottom: 16px;
    }
    .mitra-title { font-size: 2.2rem; font-weight: 800; margin: 0 0 8px; font-family: 'Playfair Display', serif; }
    .mitra-subtitle { font-size: 1.05rem; opacity: 0.8; margin: 0; display: flex; align-items: center; gap: 8px; }
    
    .section-card {
        background: white; border-radius: 20px; padding: 32px; margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f3f4f6;
    }
    .section-title { font-size: 1.3rem; font-weight: 800; color: #111827; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
    
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 24px; }
    .info-item { background: #f8fafc; padding: 16px 20px; border-radius: 16px; border: 1px solid #e2e8f0; }
    .info-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 6px; letter-spacing: 0.05em; }
    .info-value { font-size: 1.05rem; font-weight: 700; color: #0f172a; }
    
    .doc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
    .doc-card { border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; transition: 0.3s; background: white; }
    .doc-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); border-color: #cbd5e1; }
    .doc-img { width: 100%; height: 180px; object-fit: cover; background: #f1f5f9; display: block; }
    .doc-label { padding: 14px; text-align: center; font-weight: 700; color: #334155; font-size: 0.9rem; border-top: 1px solid #e2e8f0; }
    
    .campaign-list { display: grid; grid-template-columns: 1fr; gap: 16px; }
    .campaign-item { 
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; transition: 0.3s;
        text-decoration: none; background: white;
    }
    .campaign-item:hover { border-color: #6366f1; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.1); transform: translateX(5px); }
    .c-info h4 { font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0 0 6px; }
    .c-meta { font-size: 0.85rem; color: #64748b; display: flex; gap: 16px; }
    .c-status { padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
    .c-status.active { background: #dcfce7; color: #166534; }
    .c-status.pending { background: #fef3c7; color: #92400e; }
    .c-status.completed { background: #e0e7ff; color: #3730a3; }
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; margin-bottom: 20px; transition: 0.2s; }
    .btn-back:hover { color: #0f172a; }
</style>
@endsection

@section('content')

<a href="{{ url('/admin/superadmin/mitra') }}" class="btn-back">← Kembali ke Kelola Mitra</a>

<div class="mitra-detail-header">
    <div class="mitra-badge">{{ $mitra->kategori }}</div>
    <h1 class="mitra-title">{{ $mitra->nama_instansi }}</h1>
    <p class="mitra-subtitle">
        <span>📍 {{ $mitra->instansiDaerah->nama_daerah ?? 'Wilayah Tidak Diketahui' }}</span>
        <span>•</span>
        <span>📅 Terdaftar sejak {{ $mitra->created_at->format('d M Y') }}</span>
    </p>
</div>

<div class="section-card">
    <h2 class="section-title">👤 Informasi Profil</h2>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Penanggung Jawab</div>
            <div class="info-value">{{ $mitra->nama_penanggung_jawab }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Nomor Telepon / WA</div>
            <div class="info-value">{{ $mitra->no_telepon }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Email Akun</div>
            <div class="info-value">{{ $mitra->user->email }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Status Verifikasi</div>
            <div class="info-value" style="color: {{ $mitra->status_verifikasi == 'approved' ? '#16a34a' : ($mitra->status_verifikasi == 'pending' ? '#d97706' : '#dc2626') }}">
                {{ ucfirst($mitra->status_verifikasi) }}
            </div>
        </div>
    </div>
    
    <div class="info-item">
        <div class="info-label">Alamat Lengkap</div>
        <div class="info-value" style="line-height: 1.5;">{{ $mitra->alamat_lengkap }}</div>
        @if($mitra->link_maps)
            <a href="{{ $mitra->link_maps }}" target="_blank" style="display: inline-block; margin-top: 10px; color: #4f46e5; font-weight: 600; text-decoration: none; font-size: 0.9rem;">📍 Buka di Google Maps →</a>
        @endif
    </div>
    
    @if($mitra->status_verifikasi === 'approved' || $mitra->status_verifikasi === 'suspended')
    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0; display: flex; gap: 12px;">
        <form method="POST" action="{{ url('/admin/superadmin/mitra/suspend') }}" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif mitra ini?')">
            @csrf
            <input type="hidden" name="verification_id" value="{{ $mitra->id }}">
            <button class="btn" style="background: {{ $mitra->status_verifikasi === 'suspended' ? '#dcfce7' : '#fee2e2' }}; color: {{ $mitra->status_verifikasi === 'suspended' ? '#166534' : '#991b1b' }}; padding: 10px 20px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer;">
                {{ $mitra->status_verifikasi === 'suspended' ? '▶️ Aktifkan Kembali Akun' : '⏸️ Nonaktifkan Sementara (Suspend)' }}
            </button>
        </form>
    </div>
    @endif
</div>

<div class="section-card">
    <h2 class="section-title">📄 Dokumen Legalitas & Verifikasi</h2>
    <div class="doc-grid">
        <a href="{{ asset('uploads/mitra/' . $mitra->file_ktp) }}" target="_blank" class="doc-card">
            <img src="{{ asset('uploads/mitra/' . $mitra->file_ktp) }}" class="doc-img" alt="KTP">
            <div class="doc-label">KTP Penanggung Jawab</div>
        </a>
        <a href="{{ asset('uploads/mitra/' . $mitra->file_legalitas) }}" target="_blank" class="doc-card">
            <img src="{{ asset('uploads/mitra/' . $mitra->file_legalitas) }}" class="doc-img" alt="Legalitas">
            <div class="doc-label">Surat Legalitas/Izin</div>
        </a>
        <a href="{{ asset('uploads/mitra/' . $mitra->foto_bangunan) }}" target="_blank" class="doc-card">
            <img src="{{ asset('uploads/mitra/' . $mitra->foto_bangunan) }}" class="doc-img" alt="Bangunan">
            <div class="doc-label">Foto Bangunan</div>
        </a>
        <a href="{{ asset('uploads/mitra/' . $mitra->foto_kegiatan) }}" target="_blank" class="doc-card">
            <img src="{{ asset('uploads/mitra/' . $mitra->foto_kegiatan) }}" class="doc-img" alt="Kegiatan">
            <div class="doc-label">Foto Kegiatan</div>
        </a>
    </div>
</div>

<div class="section-card">
    <h2 class="section-title">📢 Kampanye Donasi yang Dibuat ({{ $campaigns->count() }})</h2>
    
    @if($campaigns->count() > 0)
        <div class="campaign-list">
            @foreach($campaigns as $camp)
                <a href="{{ url('/admin/superadmin/campaign/' . $camp->id) }}" class="campaign-item">
                    <div class="c-info">
                        <h4>{{ $camp->judul }}</h4>
                        <div class="c-meta">
                            <span>🎯 Target: {{ $camp->target_buku }} Buku</span>
                            <span>📦 Terkumpul: {{ $camp->terkumpul }} Buku</span>
                            <span>⏳ Batas: {{ $camp->batas_waktu->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <span class="c-status {{ $camp->status }}">{{ ucfirst($camp->status) }}</span>
                        <span style="color: #cbd5e1;">❯</span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 16px; color: #64748b;">
            <span style="font-size: 2.5rem; display: block; margin-bottom: 12px;">📭</span>
            <p>Mitra ini belum membuat kampanye donasi apapun.</p>
        </div>
    @endif
</div>

@endsection
