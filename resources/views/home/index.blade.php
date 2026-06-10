@extends('layouts.main')
@section('title', 'BookVerse — Buka Buku, Buka Dunia')

@php use App\Helpers\BookVerseHelper; @endphp

{{-- Hero Section (full-width, outside page-content) --}}
@section('hero')
<section class="hero">
    <div class="hero-inner">
        <div class="hero-left fade-in-up">
            <h1 class="hero-title">Book<em>Verse</em></h1>
            <div class="hero-tagline">Buka Buku, Buka Dunia.</div>
            <p class="hero-subtitle">
                BookVerse adalah komunitas pecinta buku untuk membaca,
                berbagi rekomendasi, dan menemukan dunia baru bersama.
                Satu tempat, semua tentang buku.
            </p>
            <div class="hero-actions">
                @auth
                    <a href="{{ url('/community') }}" class="btn btn-primary btn-xl">
                        👥 Jelajahi Komunitas
                    </a>
                    <a href="{{ url('/books') }}" class="btn btn-outline btn-xl">
                        📚 Cari Buku →
                    </a>
                @else
                    <a href="{{ url('/register') }}" class="btn btn-primary btn-xl">
                        👥 Gabung Sekarang
                    </a>
                    <a href="{{ url('/community') }}" class="btn btn-outline btn-xl">
                        Jelajahi Komunitas →
                    </a>
                @endauth
            </div>
        </div>
        <div class="hero-image">
            <img src="{{ asset('images/logo-bookverse.png') }}" alt="BookVerse" style="max-width:420px;width:100%;filter:drop-shadow(0 20px 40px rgba(79,60,201,0.25));">
        </div>
    </div>
</section>
@endsection

@section('content')

{{-- Feature Tiles --}}
<div class="features-bar stagger-in" style="margin-bottom:var(--space-2xl);">
    <div class="feature-item">
        <div class="feature-icon">👥</div>
        <div class="feature-title">Komunitas</div>
        <div class="feature-desc">Bergabung dan bangun komunitas sesuai minatmu.</div>
    </div>
    <div class="feature-item">
        <div class="feature-icon">📖</div>
        <div class="feature-title">Rekomendasi Buku</div>
        <div class="feature-desc">Temukan buku terbaik sesuai selera dan minatmu.</div>
    </div>
    <div class="feature-item">
        <div class="feature-icon">🏷️</div>
        <div class="feature-title">Preloved Books</div>
        <div class="feature-desc">Jual atau beli buku bekas berkualitas dengan aman.</div>
    </div>
    <div class="feature-item">
        <div class="feature-icon">🔖</div>
        <div class="feature-title">Daftar Bacaan</div>
        <div class="feature-desc">Kelola buku yang ingin, sedang, dan sudah kamu baca.</div>
    </div>
</div>

{{-- Main Content 3-Column Grid --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-xl);align-items:start;">

    {{-- COL 1: Trending Books --}}
    <div>
        <div class="section-header">
            <h2 class="section-title">📚 Buku Trending</h2>
            <a href="{{ url('/books') }}" class="view-all">Lihat Semua</a>
        </div>
        @if($trendingBooks->count() > 0)
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($trendingBooks->take(5) as $book)
                <a href="{{ url('/books/' . $book->id) }}" class="card" style="display:flex;gap:var(--space-md);text-decoration:none;padding:var(--space-md);">
                    <div class="placeholder-img" style="width:56px;height:84px;flex-shrink:0;border-radius:var(--radius-sm);">
                        @if($book->cover_buku && file_exists(public_path('uploads/covers/'.$book->cover_buku)))
                            <img src="{{ BookVerseHelper::uploadUrl('covers',$book->cover_buku) }}" alt="{{ $book->judul }}" style="width:56px;height:84px;object-fit:cover;border-radius:var(--radius-sm);">
                        @else
                            📖
                        @endif
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="book-genre-tag">{{ $book->genre_buku }}</div>
                        <div class="book-title">{{ $book->judul }}</div>
                        <div class="book-author">{{ $book->penulis }}</div>
                        {!! BookVerseHelper::starRating((float)($book->avg_rating ?? 0)) !!}
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <div class="empty-state" style="padding:var(--space-xl) 0;">
                <span class="emoji">📚</span><p>Belum ada buku trending</p>
            </div>
        @endif
    </div>

    {{-- COL 2: Popular Communities --}}
    <div>
        <div class="section-header">
            <h2 class="section-title">🏘️ Komunitas Populer</h2>
            <a href="{{ url('/community') }}" class="view-all">Lihat Semua</a>
        </div>
        @if($popularCommunities->count() > 0)
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($popularCommunities->take(5) as $community)
                <a href="{{ url('/community/' . $community->id . '/gate') }}" class="card" style="display:flex;align-items:center;gap:var(--space-md);text-decoration:none;padding:var(--space-md);">
                    <div class="placeholder-img" style="width:44px;height:44px;border-radius:50%;flex-shrink:0;font-size:1.2rem;">
                        @if($community->banner && file_exists(public_path('uploads/banners/'.$community->banner)))
                            <img src="{{ BookVerseHelper::uploadUrl('banners',$community->banner) }}" alt="" style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                        @else
                            📚
                        @endif
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="community-name" style="font-size:0.875rem;">{{ $community->nama_komunitas }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ number_format($community->member_count ?? 0) }} anggota</div>
                    </div>
                    <span style="font-size:0.75rem;color:var(--primary);">→</span>
                </a>
                @endforeach
            </div>
        @else
            <div class="empty-state" style="padding:var(--space-xl) 0;">
                <span class="emoji">🏘️</span><p>Belum ada komunitas</p>
            </div>
        @endif
    </div>

    {{-- COL 3: Review Pilihan --}}
    <div>
        <div class="section-header">
            <h2 class="section-title">⭐ Review Pilihan</h2>
            <a href="{{ url('/books') }}" class="view-all">Lihat Semua</a>
        </div>
        @if($featuredReviews->count() > 0)
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($featuredReviews->take(3) as $review)
                <div class="card" style="padding:var(--space-lg);">
                    <p style="font-size:0.85rem;color:var(--text-secondary);line-height:1.7;font-style:italic;margin-bottom:var(--space-md);">
                        "{{ BookVerseHelper::truncate($review->ulasan_teks ?? 'Buku yang sangat menarik dan menginspirasi!', 120) }}"
                    </p>
                    <div style="display:flex;align-items:center;gap:var(--space-sm);">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:0.8rem;flex-shrink:0;">
                            {{ strtoupper(substr($review->user->username ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:0.8rem;font-weight:600;color:var(--text);">{{ $review->user->username ?? 'Anonymous' }}</div>
                            {!! BookVerseHelper::starRating((float)$review->skor_rating, false) !!}
                        </div>
                        @if($review->buku)
                        <div class="placeholder-img" style="width:36px;height:54px;flex-shrink:0;margin-left:auto;border-radius:4px;font-size:0.8rem;">
                            @if($review->buku->cover_buku && file_exists(public_path('uploads/covers/'.$review->buku->cover_buku)))
                                <img src="{{ BookVerseHelper::uploadUrl('covers',$review->buku->cover_buku) }}" alt="" style="width:36px;height:54px;object-fit:cover;border-radius:4px;">
                            @else📖@endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state" style="padding:var(--space-xl) 0;">
                <span class="emoji">⭐</span><p>Belum ada review</p>
            </div>
        @endif
    </div>
</div>

{{-- CTA Banner --}}
<div style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border-radius:var(--radius-xl);padding:var(--space-xl) var(--space-2xl);margin-top:var(--space-2xl);display:flex;align-items:center;justify-content:space-between;gap:var(--space-xl);">
    <div>
        <h2 style="font-family:var(--font-display);font-size:1.4rem;color:white;margin-bottom:var(--space-sm);">
            📚 Gabung bersama ribuan pecinta buku lainnya!
        </h2>
        <p style="color:rgba(255,255,255,0.8);font-size:0.875rem;">
            Temukan teman, rekomendasi buku terbaik, dan pengalaman membaca yang lebih seru.
        </p>
    </div>
    <a href="{{ url('/register') }}" class="btn btn-white btn-xl" style="flex-shrink:0;">
        Gabung Sekarang →
    </a>
</div>

{{-- Stats Bar --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--border);border-radius:var(--radius-xl);overflow:hidden;margin-top:var(--space-xl);border:1px solid var(--border);">
    @foreach([['👥','10K+','Anggota Aktif'],['📚','25K+','Buku Terdaftar'],['⭐','15K+','Review & Penilaian'],['🌍','50K+','Rating Diberikan']] as $s)
    <div style="background:var(--text);padding:var(--space-lg);text-align:center;">
        <div style="font-size:1.3rem;margin-bottom:6px;">{{ $s[0] }}</div>
        <div style="font-size:1.2rem;font-weight:700;color:white;">{{ $s[1] }}</div>
        <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:2px;">{{ $s[2] }}</div>
    </div>
    @endforeach
</div>

@endsection
