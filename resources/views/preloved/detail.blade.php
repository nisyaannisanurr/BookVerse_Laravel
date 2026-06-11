@extends('layouts.main')
@section('title', $listing->judul_buku . ' — Preloved BookVerse')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<style>
    .preloved-detail-wrapper {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        overflow: hidden;
        margin-bottom: var(--space-2xl);
    }
    
    .preloved-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }
    
    @media (max-width: 768px) {
        .preloved-detail-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .preloved-img-section {
        background: var(--bg-surface);
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1px solid var(--border);
    }
    
    .preloved-main-img {
        width: 100%;
        max-width: 400px;
        aspect-ratio: 3/4;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
    }
    .preloved-main-img:hover {
        transform: scale(1.02);
    }
    
    .preloved-info-section {
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
    }
    
    .preloved-price-tag {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--accent);
        margin-bottom: 15px;
        line-height: 1.1;
    }
    
    .preloved-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 25px;
        line-height: 1.3;
    }
    
    .preloved-badges {
        display: flex;
        gap: 12px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .badge-condition {
        background: rgba(82, 52, 162, 0.1);
        color: var(--primary-dark);
        border: 1px solid rgba(82, 52, 162, 0.2);
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
    }
    
    .preloved-desc-box {
        background: rgba(82, 52, 162, 0.02);
        border: 1px solid rgba(82, 52, 162, 0.08);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 35px;
    }
    
    .preloved-desc-text {
        font-size: 1rem;
        color: var(--text-secondary);
        line-height: 1.8;
    }
    
    .seller-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px 0;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
        margin-bottom: 35px;
    }
    
    .seller-pic {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .wa-btn {
        background: #25D366;
        color: white;
        border: none;
        padding: 16px 30px;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3);
        transition: 0.3s ease;
        text-decoration: none;
    }
    .wa-btn:hover {
        background: #128C7E;
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(37, 211, 102, 0.4);
        color: white;
    }
    
    .owner-actions {
        background: var(--bg-surface);
        border: 1px dashed var(--border);
        border-radius: 16px;
        padding: 20px;
        margin-top: 20px;
    }
</style>

<div class="mb-md">
    <a href="{{ url('/preloved') }}" class="btn btn-ghost" style="padding:10px 20px; border-radius:50px; font-weight:600; background:var(--bg-card); box-shadow:0 2px 10px rgba(0,0,0,0.05);">← Kembali ke Eksplor</a>
</div>

<div class="preloved-detail-wrapper fade-in-up">
    <div class="preloved-detail-grid">
        <!-- Image Section -->
        <div class="preloved-img-section">
            @if($listing->foto_buku && file_exists(public_path('uploads/preloved/' . $listing->foto_buku)))
                <img src="{{ BookVerseHelper::uploadUrl('preloved', $listing->foto_buku) }}" alt="{{ $listing->judul_buku }}" class="preloved-main-img">
            @else
                <div class="preloved-main-img" style="display:flex; align-items:center; justify-content:center; background:var(--primary-light); color:var(--primary); font-size:5rem;">📖</div>
            @endif
        </div>
        
        <!-- Info Section -->
        <div class="preloved-info-section">
            <div class="preloved-price-tag">{{ BookVerseHelper::formatRupiah($listing->harga) }}</div>
            <h1 class="preloved-title">{{ $listing->judul_buku }}</h1>
            
            <div class="preloved-badges">
                <span class="badge-condition">Kondisi: {{ $listing->kondisi_buku }}</span>
                {!! BookVerseHelper::statusBadge($listing->status_buku) !!}
            </div>
            
            <div class="preloved-desc-box">
                <h4 style="margin-bottom:10px; color:var(--text); font-size:1rem;">Deskripsi Buku</h4>
                <div class="preloved-desc-text">
                    {!! nl2br(e($listing->deskripsi)) !!}
                </div>
            </div>
            
            <div class="seller-card">
                @if($listing->user && $listing->user->foto_profil)
                    <img src="{{ BookVerseHelper::uploadUrl('profiles', $listing->user->foto_profil) }}" alt="Penjual" class="seller-pic">
                @else
                    <div class="seller-pic" style="background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">👤</div>
                @endif
                <div>
                    <div style="font-size:0.85rem; color:var(--text-muted); font-weight:500; margin-bottom:3px;">Dijual oleh</div>
                    <div style="font-size:1.1rem; font-weight:700; color:var(--text);">{{ $listing->user->username ?? 'Unknown' }}</div>
                </div>
            </div>
            
            <div>
                @if($listing->status_buku === 'tersedia')
                    <a href="{{ $waLink }}" target="_blank" class="wa-btn">
                        💬 Hubungi Penjual via WhatsApp
                    </a>
                @else
                    <button class="btn btn-lg w-full" style="background:var(--bg-surface); color:var(--text-muted); font-weight:700; border-radius:50px; cursor:not-allowed;" disabled>
                        ❌ Buku Sudah Terjual
                    </button>
                @endif

                @auth
                    @if($listing->user_id !== auth()->id())
                    <button type="button" onclick="openReportModal('preloved', {{ $listing->id }})" class="btn btn-ghost w-full" style="color:var(--danger); border-radius:50px; font-weight:bold; margin-top: 15px;">
                        🚩 Laporkan Item
                    </button>
                    @endif
                @endauth
            </div>
            
            @auth
                @if($listing->user_id === auth()->id())
                <div class="owner-actions">
                    <h4 style="font-size:0.9rem; margin-bottom:15px; color:var(--text-secondary);">Tindakan Penjual</h4>
                    <div class="d-flex gap-sm flex-wrap">
                        <a href="{{ url('/preloved/' . $listing->id . '/edit') }}" class="btn btn-outline" style="border-radius:50px;">✏️ Edit Listing</a>
                        
                        @if($listing->status_buku === 'tersedia')
                        <form method="POST" action="{{ url('/preloved/sold') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                            <button type="submit" class="btn btn-accent" style="border-radius:50px;">✅ Tandai Terjual</button>
                        </form>
                        @endif
                        
                        <form method="POST" action="{{ url('/preloved/delete') }}" onsubmit="return confirm('Anda yakin ingin menghapus listing ini?')" style="display:inline; margin-left:auto;">
                            @csrf
                            <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                            <button type="submit" class="btn btn-ghost" style="color:var(--danger); border-radius:50px;">🗑️ Hapus</button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection
