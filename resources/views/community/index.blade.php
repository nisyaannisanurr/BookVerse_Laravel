@extends('layouts.main')
@section('title', 'Komunitas — BookVerse')
@php use App\Helpers\BookVerseHelper; @endphp

@section('content')

<style>
    /* Animated Gradient Background for Hero */
    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .community-hero {
        background: linear-gradient(-45deg, var(--primary-dark), var(--primary), var(--accent), var(--primary));
        background-size: 400% 400%;
        animation: gradientBG 15s ease infinite;
        border-radius: 28px;
        padding: 60px 40px;
        text-align: center;
        color: white;
        margin-bottom: var(--space-xl);
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(82, 52, 162, 0.2);
    }
    
    .hero-bg-icon {
        position: absolute;
        right: -20px;
        bottom: -20px;
        height: 120%;
        max-width: 400px;
        object-fit: contain;
        opacity: 0.35;
        z-index: 0;
        pointer-events: none;
        filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
    }

    .community-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at 20% 150%, rgba(255,255,255,0.2), transparent 50%);
        pointer-events: none;
    }

    .community-hero h1 {
        font-family: var(--font-display, 'Outfit', 'Inter', sans-serif);
        font-size: 3.2rem;
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
        text-shadow: 0 4px 15px rgba(0,0,0,0.15);
        position: relative;
        z-index: 2;
    }
    .community-hero p {
        font-size: 1.2rem;
        opacity: 0.95;
        max-width: 650px;
        margin: 0 auto 40px auto;
        line-height: 1.6;
        position: relative;
        z-index: 2;
    }

    /* Glassmorphic Search Bar */
    .search-bar-modern {
        display: flex;
        max-width: 650px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        padding: 8px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s;
    }
    .search-bar-modern:focus-within {
        transform: scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
    }
    .search-bar-modern input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 15px 25px;
        border-radius: 50px;
        font-size: 1.05rem;
        outline: none;
        color: white;
        font-weight: 500;
    }
    .search-bar-modern input::placeholder {
        color: rgba(255,255,255,0.7);
    }
    .search-bar-modern button {
        background: white;
        color: var(--primary-dark, #4f46e5);
        border: none;
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .search-bar-modern button:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255,255,255,0.3);
    }

    /* Modern Pill Tabs */
    .tabs-modern {
        display: inline-flex;
        background: var(--bg-card);
        padding: 6px;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
        gap: 5px;
    }
    .tab-modern {
        padding: 12px 28px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tab-modern:hover {
        color: var(--primary);
        background: rgba(var(--primary-rgb), 0.05);
    }
    .tab-modern.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 6px 15px rgba(var(--primary-rgb), 0.35);
    }

    /* Premium Community Cards */
    .community-card {
        border-radius: 24px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 1px solid rgba(255,255,255,0.5);
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        display: flex;
        flex-direction: column;
        height: 100%;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        position: relative;
    }
    html.dark .community-card {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(255,255,255,0.05);
    }
    .community-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.12);
        border-color: var(--primary);
    }
    .community-card-img-wrapper {
        height: 190px;
        overflow: hidden;
        position: relative;
    }
    .community-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .community-card-img-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4.5rem;
        transition: transform 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    html.dark .community-card-img-placeholder {
        background: linear-gradient(135deg, #1e293b, #0f172a);
    }
    .community-card:hover .community-card-img,
    .community-card:hover .community-card-img-placeholder {
        transform: scale(1.08);
    }
    .community-card-body {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex: 1;
        position: relative;
        z-index: 2;
        background: inherit;
    }
    .community-card-title {
        font-family: var(--font-display, 'Outfit', 'Inter', sans-serif);
        font-size: 1.4rem;
        margin-bottom: 10px;
        color: var(--text-primary);
        font-weight: 700;
        letter-spacing: -0.3px;
        transition: color 0.3s;
    }
    .community-card:hover .community-card-title {
        color: var(--primary);
    }
    .community-card-desc {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 25px;
        flex: 1;
    }
    .community-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid var(--border-color);
        padding-top: 18px;
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    .community-badge {
        background: rgba(var(--primary-rgb), 0.1);
        color: var(--primary);
        padding: 8px 18px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.8rem;
        transition: 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .community-card:hover .community-badge {
        background: var(--primary);
        color: white;
        box-shadow: 0 6px 15px rgba(var(--primary-rgb), 0.4);
    }

    /* 3D Glass CTA Banners */
    .cta-banner {
        border-radius: 24px;
        padding: 35px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
        flex-wrap: wrap;
        margin-bottom: 45px;
        position: relative;
        overflow: hidden;
        transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.4s ease;
    }
    .cta-banner:hover {
        transform: translateY(-5px);
    }
    .cta-admin {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(79, 70, 229, 0.1));
        border: 1px solid rgba(139, 92, 246, 0.2);
        box-shadow: 0 15px 30px rgba(139, 92, 246, 0.05);
        backdrop-filter: blur(12px);
    }
    .cta-admin:hover {
        box-shadow: 0 25px 50px rgba(139, 92, 246, 0.15);
        border-color: rgba(139, 92, 246, 0.4);
    }
    .cta-guest {
        background: linear-gradient(135deg, rgba(82, 52, 162, 0.05), rgba(58, 34, 120, 0.1));
        border: 1px solid rgba(82, 52, 162, 0.15);
        box-shadow: 0 15px 30px rgba(82, 52, 162, 0.05);
        backdrop-filter: blur(12px);
    }
    .cta-guest:hover {
        box-shadow: 0 25px 50px rgba(82, 52, 162, 0.12);
        border-color: rgba(82, 52, 162, 0.3);
    }

    /* Micro-animations */
    .fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.25, 0.8, 0.25, 1) forwards;
    }
    .stagger-in {
        animation: staggerIn 0.8s cubic-bezier(0.25, 0.8, 0.25, 1) forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes staggerIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

@if(($tab ?? 'explore') === 'explore')
<div style="
    background: linear-gradient(135deg, #F8F5FF 0%, #EFEAFB 100%);
    border-radius: 24px;
    padding: 0 50px;
    margin-bottom: 20px;
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
            Jelajahi Komunitas BookVerse <span style="color: var(--primary);">✨</span>
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.6;">
            Temukan teman membaca, diskusi buku favorit, dan ikuti tantangan seru di berbagai komunitas pilihan.
        </p>
    </div>
    
    <div style="position: absolute; right: 0; bottom: 0; height: 100%; width: 50%; max-width: 550px; display: flex; justify-content: flex-end;">
        <img src="{{ asset('img/rekomendasi-hero-books.png') }}" style="height: 100%; width: 100%; object-fit: cover; object-position: right bottom; mix-blend-mode: multiply; opacity: 0.95; -webkit-mask-image: linear-gradient(to right, transparent 0%, black 30%); mask-image: linear-gradient(to right, transparent 0%, black 30%);" alt="">
    </div>
</div>

<div style="
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 16px 24px;
    margin-bottom: var(--space-xl);
    display: flex;
    align-items: center;
" class="fade-in-up">
    <form method="GET" action="{{ url('/community') }}" style="display: flex; gap: 12px; width: 100%; align-items: center;">
        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); font-size: 1rem; pointer-events: none;">🔍</span>
            <input type="text" name="search" placeholder="Cari nama komunitas atau topik yang ingin kamu temukan..." value="{{ request('search') }}" style="width: 100%; border-radius: 50px; padding: 14px 20px 14px 45px; border: 1px solid var(--border); outline: none; font-size: 1rem; background: var(--bg-surface); transition: border-color 0.3s, box-shadow 0.3s; color: var(--text);">
        </div>
        @if(request('tab'))
            <input type="hidden" name="tab" value="{{ request('tab') }}">
        @endif
        <button type="submit" class="btn btn-primary" style="border-radius: 50px; padding: 12px 35px; font-weight: 700; font-size: 1rem; white-space: nowrap; box-shadow: 0 4px 15px rgba(82, 52, 162, 0.2);">Cari Komunitas</button>
        @if(request('search'))
            <a href="{{ url('/community') }}" class="btn btn-ghost" style="border-radius: 50px; padding: 12px; color: var(--danger);" title="Reset pencarian">✕</a>
        @endif
    </form>
</div>
@endif

<div class="d-flex align-center justify-between mb-lg flex-wrap" style="gap: 20px;">
    <!-- Modern Tabs -->
    <div class="tabs-modern">
        <a href="{{ url('/community?tab=explore'.(!empty($search)?'&search='.$search:'')) }}"
           class="tab-modern {{ ($tab ?? 'explore') === 'explore' ? 'active' : '' }}">
           🌐 Jelajahi Semua
        </a>
        @auth
        <a href="{{ url('/community?tab=mine'.(!empty($search)?'&search='.$search:'')) }}"
           class="tab-modern {{ ($tab ?? 'explore') === 'mine' ? 'active' : '' }}">
           🏡 Komunitas Saya
        </a>
        @endauth
    </div>

    @auth
        @if(auth()->user()->role_id <= 2)
            <a href="{{ url('/community/create') }}" class="btn btn-primary" style="border-radius: 50px; padding: 12px 25px; box-shadow: 0 4px 15px rgba(var(--primary-rgb), 0.3);">
                ➕ Buat Komunitas Baru
            </a>
        @endif
    @endauth
</div>

@if(!empty($search))
    <div style="background: rgba(139, 92, 246, 0.08); border-radius: 16px; padding: 18px 25px; margin-bottom: 35px; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(139, 92, 246, 0.2);">
        <span style="color: var(--primary); font-weight: 500; font-size: 1.05rem;">
            🔍 Menampilkan hasil pencarian untuk "<strong style="color: var(--primary-dark);">{{ $search }}</strong>" — {{ ($tab === 'mine' ? $myCommunities : $allCommunities)->count() }} komunitas ditemukan.
        </span>
        <a href="{{ url('/community?tab='.($tab ?? 'explore')) }}" class="btn btn-ghost btn-sm" style="color: #6b7280; background: white; border-radius: 50px; padding: 8px 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">✕ Hapus</a>
    </div>
@endif

{{-- ─── CTA BANNERS ──────────────── --}}
@if(($tab ?? 'explore') === 'explore' && empty($search))
    @auth
        @if(auth()->user()->role_id === 3)
        <div class="cta-banner cta-admin stagger-in">
            <div style="display:flex;align-items:center;gap:25px;">
                <div style="font-size:4rem; line-height:1; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));">👑</div>
                <div>
                    <h3 style="margin:0 0 8px 0; color:#4c1d95; font-size: 1.5rem; font-family: var(--font-display);">Ingin mengelola komunitasmu sendiri?</h3>
                    <p style="margin:0; color:#5b21b6; font-size: 1.05rem; opacity: 0.9;">Daftar jadi <strong>Admin Komunitas</strong> untuk membuka dashboard eksklusif dan mulai membangun audiensmu.</p>
                </div>
            </div>
            <div style="display:flex;gap:12px;">
                <a href="{{ url('/komunitas/daftar') }}" class="btn btn-primary" style="background: #7c3aed; box-shadow: 0 8px 20px rgba(124, 58, 237, 0.35); border:none; padding:14px 30px; border-radius:50px; font-weight: 700; font-size: 1.05rem;">🚀 Daftar Sekarang</a>
            </div>
        </div>
        @endif
    @else
        <div class="cta-banner cta-guest stagger-in">
            <div style="display:flex;align-items:center;gap:25px;">
                <div style="font-size:4rem; line-height:1; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));">✨</div>
                <div>
                    <h3 style="margin:0 0 8px 0; color:var(--primary-dark); font-size: 1.5rem; font-family: var(--font-display);">Mari bergabung bersama BookVerse!</h3>
                    <p style="margin:0; color:var(--primary); font-size: 1.05rem; opacity: 0.9;">Daftar sebagai Admin untuk membuat komunitas, atau daftar sebagai Pengguna untuk ikut bergabung.</p>
                </div>
            </div>
            <div style="display:flex;gap:15px; flex-wrap:wrap;">
                <a href="{{ url('/komunitas/daftar') }}" class="btn btn-primary" style="background: var(--primary); border-color: var(--primary); padding:14px 28px; border-radius:50px; font-weight: 700; box-shadow: 0 8px 20px rgba(82, 52, 162, 0.3);">👑 Daftar Admin</a>
                <a href="{{ url('/register') }}" class="btn btn-primary" style="background: var(--primary-dark); border-color: var(--primary-dark); padding:14px 28px; border-radius:50px; font-weight: 700; box-shadow: 0 8px 20px rgba(58, 34, 120, 0.3);">✍️ Daftar Pengguna</a>
            </div>
        </div>
    @endauth
@endif

{{-- ─── TAB: KOMUNITAS SAYA ──────────────────────── --}}
@if(($tab ?? 'explore') === 'mine')
    @auth
        @if($myCommunities->count() > 0)
            <div class="grid-3 stagger-in">
                @foreach($myCommunities as $community)
                <a href="{{ url('/community/' . $community->id . '/feed') }}" class="community-card">
                    <div class="community-card-img-wrapper">
                        @if($community->banner_komunitas && file_exists(public_path('uploads/banners/' . $community->banner_komunitas)))
                            <img src="{{ BookVerseHelper::uploadUrl('banners', $community->banner_komunitas) }}" alt="{{ $community->nama_komunitas }}" class="community-card-img">
                        @else
                            <div class="community-card-img-placeholder">🏘️</div>
                        @endif
                    </div>
                    <div class="community-card-body">
                        <h3 class="community-card-title">{{ $community->nama_komunitas }}</h3>
                        <p class="community-card-desc">{{ BookVerseHelper::truncate($community->deskripsi ?? '', 100) }}</p>
                        <div class="community-card-footer">
                            <span style="display:flex; align-items:center; gap:6px;">
                                <span style="font-size: 1.2rem;">👥</span> <strong style="color:var(--text-primary); font-size: 1.05rem;">{{ number_format($community->member_count ?? 0) }}</strong>
                            </span>
                            <span class="community-badge">Masuk Feed ➡️</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <div class="empty-state stagger-in" style="padding: 80px 20px; background: var(--bg-card); border-radius: 24px; border: 1px dashed var(--border-color);">
                <span class="emoji" style="font-size: 5rem; margin-bottom: 25px; display:inline-block; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">🏡</span>
                <h3 style="font-size: 1.8rem; margin: 15px 0; font-family: var(--font-display);">Belum ada komunitas yang kamu ikuti</h3>
                <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto 30px auto; font-size:1.1rem; line-height: 1.6;">Jelajahi berbagai komunitas menarik dan temukan teman-teman baru yang memiliki minat baca yang sama.</p>
                <a href="{{ url('/community?tab=explore') }}" class="btn btn-primary" style="padding: 14px 35px; border-radius: 50px; font-size: 1.1rem; font-weight: 600; box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);">Jelajahi Sekarang 🚀</a>
            </div>
        @endif
    @else
        <div class="empty-state stagger-in" style="padding: 80px 20px; background: var(--bg-card); border-radius: 24px; border: 1px dashed var(--border-color);">
            <span class="emoji" style="font-size: 5rem; margin-bottom: 25px; display:inline-block; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">🔑</span>
            <h3 style="font-size: 1.8rem; margin: 15px 0; font-family: var(--font-display);">Login Diperlukan</h3>
            <p style="color: var(--text-secondary); max-width: 450px; margin: 0 auto 30px auto; font-size:1.1rem; line-height: 1.6;">Silakan login terlebih dahulu untuk melihat daftar komunitas yang kamu ikuti.</p>
            <a href="{{ url('/login') }}" class="btn btn-primary" style="padding: 14px 35px; border-radius: 50px; font-size: 1.1rem; font-weight: 600; box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);">Masuk ke Akun</a>
        </div>
    @endauth

{{-- ─── TAB: JELAJAHI ────────────────────────────── --}}
@else
    @if($allCommunities->count() > 0)
        <div class="grid-3 stagger-in">
            @foreach($allCommunities as $community)
            <a href="{{ url('/community/' . $community->id) }}" class="community-card">
                <div class="community-card-img-wrapper">
                    @if($community->banner_komunitas && file_exists(public_path('uploads/banners/' . $community->banner_komunitas)))
                        <img src="{{ BookVerseHelper::uploadUrl('banners', $community->banner_komunitas) }}" alt="{{ $community->nama_komunitas }}" class="community-card-img">
                    @else
                        <div class="community-card-img-placeholder">🏘️</div>
                    @endif
                </div>
                <div class="community-card-body">
                    <h3 class="community-card-title">{{ $community->nama_komunitas }}</h3>
                    <p class="community-card-desc">{{ BookVerseHelper::truncate($community->deskripsi ?? '', 100) }}</p>
                    <div class="community-card-footer">
                        <span style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size: 1.2rem;">👥</span> <strong style="color:var(--text-primary); font-size: 1.05rem;">{{ number_format($community->member_count ?? 0) }}</strong>
                        </span>
                        <span style="color: #10b981; font-size: 0.85rem; font-weight: 700; display:flex; align-items:center; gap:6px; background: rgba(16, 185, 129, 0.15); padding: 6px 12px; border-radius: 50px;">
                            <span style="width:8px; height:8px; background:#10b981; border-radius:50%; display:inline-block; box-shadow: 0 0 8px #10b981;"></span> Aktif
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @else
        <div class="empty-state stagger-in" style="padding: 80px 20px; background: var(--bg-card); border-radius: 24px; border: 1px dashed var(--border-color);">
            <span class="emoji" style="font-size: 5rem; margin-bottom: 25px; display:inline-block; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">🏠</span>
            <h3 style="font-size: 1.8rem; margin: 15px 0; font-family: var(--font-display);">{{ !empty($search) ? 'Komunitas tidak ditemukan' : 'Belum ada komunitas' }}</h3>
            <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto 30px auto; font-size: 1.1rem; line-height: 1.6;">{{ !empty($search) ? 'Coba gunakan kata kunci lain untuk mencari komunitas yang kamu inginkan.' : 'Jadilah yang pertama membuat komunitas di BookVerse!' }}</p>
            @if(!empty($search))
                <a href="{{ url('/community') }}" class="btn btn-primary" style="padding: 14px 35px; border-radius: 50px; font-size: 1.1rem; font-weight: 600; box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);">Kembali ke Eksplor 🔙</a>
            @endif
        </div>
    @endif
@endif

@endsection
