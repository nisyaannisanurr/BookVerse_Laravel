@extends('layouts.admin')

@section('styles')
<style>
    .campaign-header {
        display: flex; gap: 32px; background: white; padding: 32px;
        border-radius: 24px; border: 1px solid #f3f4f6; margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); align-items: stretch;
    }
    .c-cover {
        width: 250px; height: 180px; object-fit: cover; border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08); flex-shrink: 0;
    }
    .c-info { flex: 1; display: flex; flex-direction: column; justify-content: center; }
    .c-title { font-size: 1.8rem; font-weight: 800; font-family: 'Playfair Display', serif; color: #111827; margin: 0 0 12px; }
    .c-status { display: inline-block; padding: 6px 14px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; margin-bottom: 16px; align-self: flex-start; }
    .c-status.active { background: #dcfce7; color: #166534; }
    .c-status.pending { background: #fef3c7; color: #92400e; }
    .c-status.completed { background: #e0e7ff; color: #3730a3; }
    
    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .stat-box { background: #f8fafc; padding: 16px; border-radius: 16px; border: 1px solid #e2e8f0; }
    .stat-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
    .stat-value { font-size: 1.2rem; font-weight: 800; color: #0f172a; }
    
    .section-card { background: white; border-radius: 24px; padding: 32px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f3f4f6; }
    .section-title { font-size: 1.3rem; font-weight: 800; color: #111827; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
    
    .donation-list { display: flex; flex-direction: column; gap: 20px; }
    .donation-card {
        border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px;
        background: #fff; transition: 0.3s; display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
    }
    .donation-card:hover { border-color: #cbd5e1; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    
    .d-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .d-avatar { width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; object-fit: cover; }
    .d-name { font-weight: 700; color: #0f172a; font-size: 1.05rem; margin: 0; }
    .d-date { font-size: 0.8rem; color: #64748b; }
    
    .d-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
    .d-info-item { background: #f8fafc; padding: 12px; border-radius: 10px; }
    .d-info-label { font-size: 0.7rem; color: #64748b; font-weight: 700; margin-bottom: 4px; }
    .d-info-value { font-size: 0.95rem; font-weight: 600; color: #1e293b; }
    
    .review-box {
        background: #fdf4ff; border: 1px solid #f5d0fe; border-radius: 16px; padding: 20px;
        display: flex; gap: 20px; align-items: flex-start;
    }
    .review-icon { font-size: 2rem; }
    .review-content { flex: 1; }
    .review-title { font-weight: 700; color: #86198f; margin: 0 0 8px; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .review-text { color: #4a044e; font-size: 0.95rem; line-height: 1.5; margin: 0 0 16px; font-style: italic; }
    .review-img { width: 100px; height: 100px; object-fit: cover; border-radius: 12px; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; margin-bottom: 20px; transition: 0.2s; }
    .btn-back:hover { color: #0f172a; }
</style>
@endsection

@section('content')

@php
    $mitra = $campaign->user->mitraVerification ?? null;
    $mitraId = $mitra ? $mitra->id : 0;
@endphp

<a href="{{ url('/admin/superadmin/mitra/' . $mitraId) }}" class="btn-back">← Kembali ke Profil Mitra</a>

<div class="campaign-header">
    <img src="{{ asset('uploads/campaigns/' . $campaign->foto_campaign) }}" alt="Cover" class="c-cover">
    <div class="c-info">
        <span class="c-status {{ $campaign->status }}">{{ ucfirst($campaign->status) }}</span>
        <h1 class="c-title">{{ $campaign->judul }}</h1>
        <div class="stat-grid">
            <div class="stat-box">
                <div class="stat-label">Target Buku</div>
                <div class="stat-value">{{ $campaign->target_buku }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Terkumpul</div>
                <div class="stat-value" style="color: #059669;">{{ $campaign->terkumpul }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Batas Waktu</div>
                <div class="stat-value">{{ $campaign->batas_waktu->format('d M Y') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="section-card">
    <h2 class="section-title">🎁 Daftar Donatur & Review Mitra ({{ $donations->count() }})</h2>
    
    @if($donations->count() > 0)
        <div class="donation-list">
            @foreach($donations as $d)
                <div class="donation-card">
                    <!-- Info Donatur -->
                    <div>
                        <div class="d-header">
                            <img src="{{ $d->user && $d->user->foto_profil ? asset('uploads/profiles/' . $d->user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($d->user->username ?? 'Donatur') }}" class="d-avatar" alt="User">
                            <div>
                                <h3 class="d-name">{{ $d->user->username ?? 'Donatur Anonim' }}</h3>
                                <div class="d-date">{{ $d->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="d-info-grid">
                            <div class="d-info-item">
                                <div class="d-info-label">Judul Buku Disumbangkan</div>
                                <div class="d-info-value">{{ $d->judul_buku }}</div>
                            </div>
                            <div class="d-info-item">
                                <div class="d-info-label">Jumlah & Kondisi</div>
                                <div class="d-info-value">{{ $d->jumlah }} Buku ({{ ucfirst($d->kondisi) }})</div>
                            </div>
                            <div class="d-info-item">
                                <div class="d-info-label">Status Pengiriman</div>
                                <div class="d-info-value" style="color: {{ $d->status_color }};">{{ $d->status_label }}</div>
                            </div>
                            <div class="d-info-item">
                                <div class="d-info-label">Resi Pengiriman</div>
                                <div class="d-info-value">{{ $d->resi_pengiriman ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Review / Pesan Terima dari Mitra -->
                    @if($d->status_pengiriman == 'diterima')
                        <div class="review-box">
                            <div class="review-icon">💌</div>
                            <div class="review-content">
                                <h4 class="review-title">Review & Pesan Terima dari Mitra</h4>
                                <p class="review-text">"{{ $d->pesan_terima ?? 'Terima kasih banyak atas donasinya! Buku telah kami terima dengan baik.' }}"</p>
                                
                                @if($d->foto_terima)
                                    <a href="{{ asset('uploads/donasi_bukti/' . $d->foto_terima) }}" target="_blank">
                                        <img src="{{ asset('uploads/donasi_bukti/' . $d->foto_terima) }}" class="review-img" alt="Foto Terima">
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <div style="display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 16px; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 0.9rem; text-align: center; padding: 20px;">
                            <div>
                                <span style="font-size: 2rem; display: block; margin-bottom: 8px;">⏳</span>
                                Menunggu donasi diterima oleh mitra untuk melihat review.
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 16px; color: #64748b;">
            <span style="font-size: 2.5rem; display: block; margin-bottom: 12px;">😔</span>
            <p>Belum ada donatur yang menyumbangkan buku untuk kampanye ini.</p>
        </div>
    @endif
</div>

@endsection
