@extends('layouts.main')
@section('title', 'Preloved Books — BookVerse')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<style>
    /* Hero and filter styles are now inline */
    
    .premium-preloved-card {
        border-radius: 20px;
        overflow: hidden;
        background: var(--bg-card);
        border: 1px solid var(--border);
        transition: 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        text-decoration: none;
        color: inherit;
        position: relative;
    }
    .premium-preloved-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: var(--primary-light);
    }
    .premium-preloved-img-wrap {
        aspect-ratio: 4/5;
        overflow: hidden;
        background: var(--bg-surface);
        position: relative;
    }
    .premium-preloved-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.5s ease;
    }
    .premium-preloved-card:hover .premium-preloved-img {
        transform: scale(1.05);
    }
    .condition-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary-dark);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        z-index: 2;
    }
    .premium-preloved-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .premium-preloved-price {
        font-family: var(--font-display);
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--accent);
        margin-bottom: 5px;
    }
    .premium-preloved-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .premium-preloved-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px dashed var(--border);
        padding-top: 15px;
    }
    .seller-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
    }
    .seller-name {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 500;
    }
</style>

{{-- ─── HEADER BANNER ────────────────────────────────────── --}}
<div style="
    background: var(--gradient-light);
    border-radius: 24px;
    padding: 0 50px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    min-height: 200px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.03);
" class="fade-in-up">
    <div style="position: relative; z-index: 2; max-width: 500px; padding: 40px 0;">
        <h1 style="font-family: var(--font-display); font-size: 2.4rem; font-weight: 800; color: var(--text); margin-bottom: 10px;">
            Buku Preloved Berkualitas <span style="color: var(--primary);">✨</span>
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.6; margin-bottom: 25px;">
            Temukan buku-buku bekas berkualitas dari sesama pembaca, atau jual buku yang sudah selesai kamu baca. Lebih hemat, lebih ramah lingkungan!
        </p>
        @auth
            <a href="{{ url('/preloved/create') }}" class="btn btn-primary" style="padding: 12px 28px; border-radius: 50px; font-weight: 700; font-size: 1.05rem; box-shadow: 0 8px 20px rgba(82, 52, 162, 0.2);">
                🏷️ Jual Buku Saya
            </a>
        @else
            <a href="{{ url('/login') }}" class="btn btn-primary" style="padding: 12px 28px; border-radius: 50px; font-weight: 700; font-size: 1.05rem; box-shadow: 0 8px 20px rgba(82, 52, 162, 0.2);">
                Masuk untuk Menjual
            </a>
        @endauth
    </div>
    
    <div style="position: absolute; right: 0; bottom: 0; height: 100%; width: 50%; max-width: 550px; display: flex; justify-content: flex-end;">
        <img src="{{ asset('img/rekomendasi-hero-books.png') }}" style="height: 100%; width: 100%; object-fit: cover; object-position: right bottom; mix-blend-mode: multiply; opacity: 0.95; -webkit-mask-image: linear-gradient(to right, transparent 0%, black 30%); mask-image: linear-gradient(to right, transparent 0%, black 30%);" alt="">
    </div>
</div>

{{-- ─── FILTER ROW ───────────────────────────────────────── --}}
<div style="
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 16px 24px;
    margin-bottom: var(--space-xl);
" class="fade-in-up">
    <form method="GET" action="{{ url('/preloved') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
        <div style="position: relative; flex: 2; min-width: 250px;">
            <span style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); font-size: 1rem; pointer-events: none;">🔍</span>
            <input type="text" name="search" placeholder="Cari judul atau penulis buku..." value="{{ $filters['search'] ?? '' }}" style="width: 100%; border-radius: 50px; padding: 14px 20px 14px 45px; border: 1px solid var(--border); outline: none; font-size: 1rem; background: var(--bg-surface); transition: border-color 0.3s, box-shadow 0.3s; color: var(--text);">
        </div>
        
        <select name="kondisi" style="flex: 1; min-width: 150px; border-radius: 50px; padding: 14px 20px; border: 1px solid var(--border); outline: none; font-size: 0.95rem; background: var(--bg-surface) url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e') no-repeat right 1.2rem center/1em; color: var(--text); appearance: none;">
            <option value="">Semua Kondisi</option>
            <option value="Seperti Baru" {{ ($filters['kondisi'] ?? '') === 'Seperti Baru' ? 'selected' : '' }}>Seperti Baru</option>
            <option value="Baik" {{ ($filters['kondisi'] ?? '') === 'Baik' ? 'selected' : '' }}>Baik</option>
            <option value="Cukup" {{ ($filters['kondisi'] ?? '') === 'Cukup' ? 'selected' : '' }}>Cukup</option>
        </select>
        
        <div style="display:flex; gap:10px; flex: 1.5; min-width: 220px;">
            <input type="text" inputmode="numeric" name="min_harga" class="currency-input" placeholder="Min Rp" value="{{ $filters['min_harga'] ?? '' }}" style="width: 50%; border-radius: 50px; padding: 14px 20px; border: 1px solid var(--border); outline: none; font-size: 0.95rem; background: var(--bg-surface); color: var(--text);">
            <input type="text" inputmode="numeric" name="max_harga" class="currency-input" placeholder="Max Rp" value="{{ $filters['max_harga'] ?? '' }}" style="width: 50%; border-radius: 50px; padding: 14px 20px; border: 1px solid var(--border); outline: none; font-size: 0.95rem; background: var(--bg-surface); color: var(--text);">
        </div>
        
        <button type="submit" class="btn btn-primary" style="border-radius: 50px; padding: 14px 30px; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(82, 52, 162, 0.2);">Filter</button>
        @if(!empty($filters['search']) || !empty($filters['kondisi']) || !empty($filters['min_harga']) || !empty($filters['max_harga']))
            <a href="{{ url('/preloved') }}" class="btn btn-ghost" style="border-radius: 50px; padding: 14px; color: var(--danger);" title="Reset filter">✕</a>
        @endif
    </form>
</div>

<!-- Listings Grid -->
@if($listings->count() > 0)
    <div class="grid-4 stagger-in">
        @foreach($listings as $item)
            <a href="{{ url('/preloved/' . $item->id) }}" class="premium-preloved-card">
                <div class="premium-preloved-img-wrap">
                    <span class="condition-badge">{{ $item->kondisi_buku }}</span>
                    @if($item->foto_buku && file_exists(public_path('uploads/preloved/' . $item->foto_buku)))
                        <img src="{{ BookVerseHelper::uploadUrl('preloved', $item->foto_buku) }}" alt="{{ $item->judul_buku }}" class="premium-preloved-img">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:3rem;background:var(--primary-light);color:var(--primary);">📖</div>
                    @endif
                </div>
                <div class="premium-preloved-body">
                    <div class="premium-preloved-price">{{ BookVerseHelper::formatRupiah($item->harga) }}</div>
                    <div class="premium-preloved-title">{{ $item->judul_buku }}</div>
                    <div class="premium-preloved-footer">
                        <div class="seller-avatar">👤</div>
                        <div class="seller-name">{{ $item->user->username ?? 'Penjual' }}</div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="empty-state stagger-in" style="background: var(--bg-card); border-radius: 24px; padding: 60px 20px; border: 1px dashed var(--border);">
        <span class="emoji" style="font-size: 5rem; margin-bottom: 20px; display:inline-block;">🏷️</span>
        <h3 style="font-family: var(--font-display); font-size: 1.8rem; margin-bottom: 15px;">Belum ada buku preloved</h3>
        <p style="color: var(--text-secondary); font-size: 1.05rem; max-width: 500px; margin: 0 auto 25px;">Jadilah yang pertama menjual buku yang sudah selesai kamu baca di BookVerse!</p>
        @auth
            <a href="{{ url('/preloved/create') }}" class="btn btn-primary" style="padding: 14px 35px; border-radius: 50px; font-weight: 700; box-shadow: 0 8px 20px rgba(82, 52, 162, 0.25);">
                Mulai Jual Buku
            </a>
        @endauth
    </div>
@endif
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.currency-input').forEach(input => {
        // Format initial value
        if(input.value) {
            let val = input.value.replace(/[^0-9]/g, '');
            if (val) input.value = parseInt(val, 10).toLocaleString('id-ID');
        }
        
        // Format on input
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9]/g, '');
            if (val) {
                this.value = parseInt(val, 10).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
        });
    });
});
</script>
