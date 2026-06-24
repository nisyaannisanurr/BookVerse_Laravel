@extends('layouts.main')
@section('title', 'Donasi Buku — BookVerse')
@section('meta_description', 'Donasikan buku bekas layak baca untuk sekolah dan taman bacaan yang membutuhkan. Bersama BookVerse, sebarkan semangat literasi!')
@section('content')

<style>
    .donasi-hero {
        background: var(--gradient-donasi);
        border-radius: 24px;
        padding: 0 50px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        min-height: 220px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.03);
    }
    .donasi-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 32px;
    }
    .donasi-stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        transition: 0.3s;
    }
    .donasi-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    }
    .donasi-stat-value {
        font-family: var(--font-display);
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
    }
    .donasi-stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
    }
    .campaign-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        transition: 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        text-decoration: none;
        color: inherit;
    }
    .campaign-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: #f59e0b;
    }
    .campaign-img-wrap {
        aspect-ratio: 16/9;
        background: var(--gradient-donasi);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        position: relative;
        overflow: hidden;
    }
    .campaign-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .campaign-kategori-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(4px);
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--primary-dark);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        z-index: 2;
    }
    .campaign-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .campaign-title {
        font-family: var(--font-display);
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .campaign-mitra {
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .campaign-progress {
        margin-top: auto;
    }
    .progress-bar-wrap {
        background: #f3f4f6;
        border-radius: 50px;
        height: 10px;
        overflow: hidden;
        margin-bottom: 8px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 50px;
        background: linear-gradient(90deg, var(--primary-light), var(--primary));
        transition: width 0.6s ease;
    }
    .progress-info {
        display: flex;
        justify-content: space-between;
        font-size: 0.78rem;
        color: var(--text-muted);
    }
    .progress-info strong {
        color: var(--primary);
    }
    .campaign-footer {
        border-top: 1px dashed var(--border);
        padding-top: 14px;
        margin-top: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    @media (max-width: 768px) {
        .donasi-hero { padding: 30px 20px; min-height: 160px; }
        .donasi-stats-grid { grid-template-columns: 1fr; }
    }
</style>

{{-- ─── HERO ─────────────────────────────────────── --}}
<div class="donasi-hero fade-in-up">
    <div style="position: relative; z-index: 2; max-width: 520px; padding: 40px 0;">
        <h1 style="font-family: var(--font-display); font-size: 2.4rem; font-weight: 800; color: var(--text); margin-bottom: 10px;">
            Donasi Buku <span style="color: var(--primary);">📚💜</span>
        </h1>
        <p style="color: #4b5563; font-size: 1.1rem; line-height: 1.6; margin-bottom: 25px;">
            Bantu sebarkan semangat literasi! Donasikan buku bekas layak baca untuk sekolah, panti asuhan, dan taman bacaan masyarakat yang membutuhkan.
        </p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @auth
                @if(auth()->user()->isMitra())
                    <a href="{{ url('/donasi/dashboard') }}" class="btn btn-primary" style="padding: 12px 28px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);">
                        🏫 Dashboard Mitra
                    </a>
                @elseif(auth()->user()->isSuperadmin())
                    <a href="{{ url('/admin/superadmin/mitra') }}" class="btn btn-primary" style="padding: 12px 28px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);">
                        🤝 Kelola Mitra
                    </a>
                @else
                    <a href="{{ url('/mitra/daftar') }}" class="btn btn-primary" style="padding: 12px 28px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);">
                        🤝 Daftar Sebagai Mitra
                    </a>
                @endif
            @else
                <a href="{{ url('/mitra/daftar') }}" class="btn btn-primary" style="padding: 12px 28px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);">
                    🤝 Daftar Sebagai Mitra
                </a>
            @endauth
        </div>
    </div>
    <div style="position: absolute; right: -20px; bottom: -20px; font-size: 12rem; opacity: 0.08; pointer-events: none;">📚</div>
</div>

{{-- ─── STATS ────────────────────────────────────── --}}
<div class="donasi-stats-grid fade-in-up">
    <div class="donasi-stat-card">
        <div class="donasi-stat-value" style="color: #d97706;">{{ $totalCampaign }}</div>
        <div class="donasi-stat-label">Kampanye Aktif</div>
    </div>
    <div class="donasi-stat-card">
        <div class="donasi-stat-value" style="color: #16a34a;">{{ $totalBukuDiterima }}</div>
        <div class="donasi-stat-label">Buku Telah Disalurkan</div>
    </div>
    <div class="donasi-stat-card">
        <div class="donasi-stat-value" style="color: #7c3aed;">{{ $totalDonatur }}</div>
        <div class="donasi-stat-label">Donatur</div>
    </div>
</div>

{{-- ─── CAMPAIGN LIST ────────────────────────────── --}}
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;" class="fade-in-up">
    <h2 style="font-family: var(--font-display); font-size: 1.6rem; font-weight: 700; color: var(--text);">
        Kampanye yang Membutuhkan Bantuan
    </h2>
</div>

{{-- ─── WIDE SEARCH BAR ────────────────────────────────────── --}}
<div style="
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 16px 24px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
" class="fade-in-up">
    <form method="GET" action="{{ url('/donasi') }}" id="mainSearchForm" style="display: flex; gap: 12px; width: 100%; align-items: center; flex-wrap: wrap;">
        <div style="position: relative; flex: 1; min-width: 250px;">
            <span style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); font-size: 1rem; pointer-events: none;">🔍</span>
            <input type="text" name="search" placeholder="Cari nama kampanye atau daerah..." value="{{ request('search') }}" style="width: 100%; border-radius: 50px; padding: 14px 20px 14px 45px; border: 1px solid var(--border); outline: none; font-size: 1rem; background: var(--bg-surface); transition: border-color 0.3s, box-shadow 0.3s; color: var(--text);">
        </div>
        
        <select name="kategori" style="border-radius: 50px; padding: 14px 20px; border: 1px solid var(--border); font-weight: 600; color: var(--text-secondary); background: var(--bg-surface); font-size: 0.95rem; outline: none; cursor: pointer;">
            <option value="">≢ Semua Kategori</option>
            <option value="Sekolah" {{ request('kategori') == 'Sekolah' ? 'selected' : '' }}>Sekolah</option>
            <option value="Taman Bacaan Masyarakat" {{ request('kategori') == 'Taman Bacaan Masyarakat' ? 'selected' : '' }}>Taman Bacaan</option>
            <option value="Panti Asuhan" {{ request('kategori') == 'Panti Asuhan' ? 'selected' : '' }}>Panti Asuhan</option>
            <option value="Lainnya" {{ request('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
        
        <button type="submit" class="btn btn-primary" style="border-radius: 50px; padding: 12px 35px; font-weight: 700; font-size: 1rem; white-space: nowrap; box-shadow: 0 4px 15px rgba(var(--primary-rgb), 0.2);">Cari</button>
        
        @if(request('search') || request('kategori'))
            <a href="{{ url('/donasi') }}" class="btn btn-ghost" style="border-radius: 50px; padding: 12px; color: var(--danger); background: var(--bg-surface);" title="Reset pencarian">✕</a>
        @endif
    </form>
</div>

@if($campaigns->count() > 0)
    <div class="grid-3 stagger-in">
        @foreach($campaigns as $campaign)
            @php
                $mitra = $campaign->user->mitraVerification ?? null;
            @endphp
            <a href="{{ url('/donasi/' . $campaign->id) }}" class="campaign-card" id="campaign-{{ $campaign->id }}">
                <div class="campaign-img-wrap">
                    @if($campaign->foto_campaign && file_exists(public_path('uploads/campaigns/' . $campaign->foto_campaign)))
                        <img src="{{ asset('uploads/campaigns/' . $campaign->foto_campaign) }}" alt="{{ $campaign->judul }}">
                    @else
                        📚
                    @endif
                    @if($mitra)
                        <span class="campaign-kategori-badge">{{ $mitra->kategori }}</span>
                    @endif
                </div>
                <div class="campaign-body">
                    <div class="campaign-title">{{ $campaign->judul }}</div>
                    <div class="campaign-mitra">
                        <span>🏫</span>
                        {{ $mitra->nama_instansi ?? 'Mitra' }}
                    </div>

                    <div class="campaign-progress">
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width: {{ $campaign->progress_percent }}%"></div>
                        </div>
                        <div class="progress-info">
                            <span><strong>{{ $campaign->terkumpul }}</strong> / {{ $campaign->target_buku }} buku</span>
                            <span>{{ $campaign->progress_percent }}%</span>
                        </div>
                    </div>

                    <div class="campaign-footer">
                        <span>⏰ {{ $campaign->sisaHari() }} hari lagi</span>
                        <span style="color: #d97706; font-weight: 600;">Donasi →</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="empty-state stagger-in" style="background: var(--bg-card); border-radius: 24px; padding: 60px 20px; border: 1px dashed var(--border); text-align: center;">
        <span style="font-size: 5rem; margin-bottom: 20px; display:inline-block;">📦</span>
        <h3 style="font-family: var(--font-display); font-size: 1.8rem; margin-bottom: 15px;">Belum ada kampanye aktif</h3>
        <p style="color: var(--text-secondary); font-size: 1.05rem; max-width: 500px; margin: 0 auto 25px;">
            Kampanye donasi buku dari sekolah dan taman bacaan akan tampil di sini. Daftarkan instansi Anda untuk membuat kampanye!
        </p>
        @auth
            @if(auth()->user()->isMitra())
                <a href="{{ url('/donasi/dashboard') }}" class="btn btn-primary" style="padding: 14px 35px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);">
                    🏫 Dashboard Mitra
                </a>
            @elseif(auth()->user()->isSuperadmin())
                <a href="{{ url('/admin/superadmin/mitra') }}" class="btn btn-primary" style="padding: 14px 35px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);">
                    🤝 Kelola Mitra
                </a>
            @else
                <a href="{{ url('/mitra/daftar') }}" class="btn btn-primary" style="padding: 14px 35px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);">
                    Daftar Sebagai Mitra
                </a>
            @endif
        @else
            <a href="{{ url('/mitra/daftar') }}" class="btn btn-primary" style="padding: 14px 35px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);">
                Daftar Sebagai Mitra
            </a>
        @endauth
    </div>
@endif

@endsection
