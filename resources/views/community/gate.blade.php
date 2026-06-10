@extends('layouts.main')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<style>
    .gate-container {
        max-width: 800px;
        margin: 0 auto;
        border-radius: 24px;
        overflow: hidden;
        background: var(--bg-card);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border: 1px solid rgba(82, 52, 162, 0.1);
        position: relative;
    }
    
    .gate-banner-area {
        position: relative;
        height: 280px;
        width: 100%;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        overflow: hidden;
    }
    
    .gate-banner-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.85;
        transition: transform 0.5s ease;
    }
    
    .gate-banner-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, var(--bg-card) 0%, transparent 100%);
        z-index: 1;
    }

    .gate-content {
        padding: 0 40px 40px;
        position: relative;
        z-index: 2;
        text-align: center;
        margin-top: -60px;
    }

    .gate-lock-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--accent), #f59e0b);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 20px;
        box-shadow: 0 10px 25px rgba(196, 152, 86, 0.4);
        border: 4px solid var(--bg-card);
        color: white;
    }

    .gate-title {
        font-family: var(--font-display);
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }

    .gate-desc {
        font-size: 1.05rem;
        color: var(--text-secondary);
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto 25px;
    }

    .gate-stats {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 30px;
        margin-bottom: 35px;
        padding: 15px 30px;
        background: var(--bg-surface);
        border-radius: 50px;
        display: inline-flex;
        border: 1px solid var(--border);
    }
    
    .gate-stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-weight: 500;
        font-size: 0.95rem;
    }
    
    .gate-stat-item strong {
        color: var(--text);
    }

    .gate-rules {
        background: rgba(82, 52, 162, 0.03);
        border: 1px solid rgba(82, 52, 162, 0.1);
        border-radius: 20px;
        padding: 30px;
        text-align: left;
        margin-bottom: 35px;
    }
    
    .gate-rules h4 {
        color: var(--primary-dark);
        font-size: 1.1rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .gate-rules-text {
        color: var(--text-secondary);
        font-size: 0.95rem;
        line-height: 1.8;
    }

    .gate-action-area {
        padding-top: 20px;
        border-top: 1px dashed var(--border);
    }
</style>

<div class="fade-in-up">
    <div class="gate-container">
        <!-- Banner Area -->
        <div class="gate-banner-area">
            @if($community->banner_komunitas && file_exists(public_path('uploads/banners/' . $community->banner_komunitas)))
                <img src="{{ BookVerseHelper::uploadUrl('banners', $community->banner_komunitas) }}" alt="Banner Komunitas" class="gate-banner-img">
            @else
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:5rem;opacity:0.2;">🏘️</div>
            @endif
            <div class="gate-banner-overlay"></div>
        </div>

        <!-- Content Area -->
        <div class="gate-content">
            <div class="gate-lock-icon">
                🔒
            </div>
            
            <h2 class="gate-title">{{ $community->nama_komunitas }}</h2>
            <p class="gate-desc">{{ $community->deskripsi }}</p>

            <div class="gate-stats">
                <div class="gate-stat-item">
                    <span>👥</span> <strong>{{ number_format($community->member_count ?? 0) }}</strong> Anggota
                </div>
                <div class="gate-stat-item">
                    <span>👑</span> Dibuat oleh <strong>{{ $community->creator->username ?? 'Unknown' }}</strong>
                </div>
            </div>

            @if(!empty($community->peraturan))
            <div class="gate-rules">
                <h4><span style="font-size:1.3rem;">📋</span> Peraturan Komunitas</h4>
                <div class="gate-rules-text">{!! nl2br(e($community->peraturan)) !!}</div>
            </div>
            @endif

            <div class="gate-action-area">
                @auth
                    @if($memberStatus === 'pending')
                        <button class="btn" style="background:var(--bg-surface); color:var(--text-muted); padding:14px 35px; border-radius:50px; font-weight:700; cursor:not-allowed;" disabled>
                            ⏳ Menunggu Persetujuan
                        </button>
                    @elseif($memberStatus === 'rejected')
                        <div style="background:rgba(220, 38, 38, 0.1); color:var(--danger); padding:10px; border-radius:12px; margin-bottom:15px; font-weight:500;">
                            ❌ Permintaan bergabung Anda ditolak.
                        </div>
                        <form method="POST" action="{{ url('/community/' . $community->id . '/join') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="padding:14px 35px; border-radius:50px; font-weight:700; box-shadow: 0 8px 20px rgba(82, 52, 162, 0.25);">
                                🔄 Ajukan Lagi
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ url('/community/' . $community->id . '/join') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="padding:15px 40px; border-radius:50px; font-size:1.05rem; font-weight:700; box-shadow: 0 10px 25px rgba(82, 52, 162, 0.3);">
                                🤝 Gabung Komunitas
                            </button>
                        </form>
                    @endif
                @else
                    <h3 style="font-family:var(--font-display); color:var(--primary-dark); margin-bottom:10px;">Tertarik untuk bergabung?</h3>
                    <p style="color:var(--text-secondary); font-size:0.9rem; margin-bottom:20px;">Daftar akun BookVerse secara gratis untuk ikut berdiskusi di komunitas ini.</p>
                    <a href="{{ url('/login') }}" class="btn btn-primary" style="padding:14px 35px; border-radius:50px; font-weight:700; box-shadow: 0 8px 20px rgba(82, 52, 162, 0.25);">
                        Masuk / Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- ─── CTA: BUAT KOMUNITASMU SENDIRI ─────────────────── --}}
@auth
    @if(auth()->user()->role_id === 3)
    <div style="
        margin-top: var(--space-2xl);
        background: linear-gradient(135deg, rgba(82, 52, 162, 0.05) 0%, rgba(58, 34, 120, 0.1) 100%);
        border: 1px solid rgba(82, 52, 162, 0.15);
        border-radius: var(--radius-xl);
        padding: var(--space-xl);
        text-align: center;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 15px 30px rgba(82, 52, 162, 0.05);
    ">
        <div style="font-size: 2.5rem; margin-bottom: 15px;">👑</div>
        <h3 style="font-family: var(--font-display); font-size: 1.4rem; font-weight: 700; margin-bottom: 12px; color: var(--primary-dark);">
            Mau jadi Admin Komunitas?
        </h3>
        <p style="font-size: 0.95rem; color: var(--text-secondary); margin-bottom: var(--space-lg); line-height: 1.6;">
            Daftarkan komunitasmu sendiri — gratis! Isi form singkat, dan kamu langsung
            menjadi <strong>Admin Komunitas</strong>. Komunitas aktif setelah disetujui Superadmin.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ url('/komunitas/daftar') }}" class="btn btn-primary" style="padding:12px 25px; border-radius:50px; font-weight:700;">
                ✨ Buat Komunitasku Sekarang
            </a>
            <a href="{{ url('/komunitas/masuk') }}" class="btn btn-outline" style="padding:12px 25px; border-radius:50px; font-weight:700;">
                Sudah punya akun admin?
            </a>
        </div>
        <div style="margin-top: 25px; display: flex; gap: var(--space-xl); justify-content: center; font-size: 0.8rem; color: var(--primary);">
            <span style="display:flex; align-items:center; gap:5px;"><span style="color:var(--accent);">✓</span> Gratis</span>
            <span style="display:flex; align-items:center; gap:5px;"><span style="color:var(--accent);">✓</span> Langsung jadi admin</span>
            <span style="display:flex; align-items:center; gap:5px;"><span style="color:var(--accent);">✓</span> Disetujui Superadmin</span>
        </div>
    </div>
    @endif
@else
    {{-- Guest: tampilkan CTA daftar akun --}}
    <div style="
        margin-top: var(--space-2xl);
        background: linear-gradient(135deg, rgba(82, 52, 162, 0.05) 0%, rgba(58, 34, 120, 0.1) 100%);
        border: 1px solid rgba(82, 52, 162, 0.15);
        border-radius: var(--radius-xl);
        padding: var(--space-xl);
        text-align: center;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 15px 30px rgba(82, 52, 162, 0.05);
    ">
        <div style="font-size: 2.5rem; margin-bottom: 15px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">✨</div>
        <h3 style="font-family: var(--font-display); font-size: 1.4rem; font-weight: 700; margin-bottom: 12px; color: var(--primary-dark);">
            Punya minat yang sama?
        </h3>
        <p style="font-size: 0.95rem; color: var(--text-secondary); margin-bottom: var(--space-lg); line-height: 1.6;">
            Daftar akun BookVerse dan buat komunitasmu sendiri sebagai <strong>Admin Komunitas</strong>.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ url('/register') }}" class="btn btn-primary" style="padding:12px 25px; border-radius:50px; font-weight:700;">✍️ Daftar Pengguna</a>
            <a href="{{ url('/komunitas/daftar') }}" class="btn btn-outline" style="padding:12px 25px; border-radius:50px; font-weight:700;">👑 Daftar Admin</a>
            <a href="{{ url('/login') }}" class="btn btn-ghost" style="padding:12px 20px; border-radius:50px; font-weight:600;">Masuk</a>
        </div>
    </div>
@endauth

@endsection
