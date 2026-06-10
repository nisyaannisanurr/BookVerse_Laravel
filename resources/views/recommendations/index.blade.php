@extends('layouts.main')
@section('title', 'Rekomendasi Buku — BookVerse')
@php use App\Helpers\BookVerseHelper; @endphp

@section('content')

{{-- ─── HEADER BANNER ────────────────────────────────────── --}}
<div style="
    background: linear-gradient(135deg, #F8F5FF 0%, #EFEAFB 100%);
    border-radius: 24px;
    padding: 0 50px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    min-height: 180px;
">
    <div style="position: relative; z-index: 2; max-width: 500px; padding: 40px 0;">
        <h1 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; color: var(--text); margin-bottom: 8px;">
            Rekomendasi Buku <span style="color: var(--primary);">✨</span>
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.05rem; line-height: 1.6;">
            Temukan buku terbaik sesuai genre favorit dan minat bacamu.
        </p>
    </div>
    
    <div style="position: absolute; right: 0; bottom: 0; height: 100%; width: 50%; max-width: 550px; display: flex; justify-content: flex-end;">
        <img src="{{ asset('img/rekomendasi-hero-books.png') }}" style="height: 100%; width: 100%; object-fit: cover; object-position: right bottom; mix-blend-mode: multiply; -webkit-mask-image: linear-gradient(to right, transparent 0%, black 30%); mask-image: linear-gradient(to right, transparent 0%, black 30%);" alt="">
    </div>
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
    <form method="GET" action="{{ url('/recommendations') }}" id="mainSearchForm" style="display: flex; gap: 12px; width: 100%; align-items: center;">
        <div style="position: relative; flex: 1;">
            <span style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); font-size: 1rem; pointer-events: none;">🔍</span>
            <input type="text" name="search" placeholder="Cari judul, penulis, atau genre buku..." value="{{ $search ?? '' }}" style="width: 100%; border-radius: 50px; padding: 14px 20px 14px 45px; border: 1px solid var(--border); outline: none; font-size: 1rem; background: var(--bg-surface); transition: border-color 0.3s, box-shadow 0.3s; color: var(--text);">
        </div>
        @if(request('genre'))
            <input type="hidden" name="genre" value="{{ request('genre') }}">
        @endif
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        <button type="submit" class="btn btn-primary" style="border-radius: 50px; padding: 12px 35px; font-weight: 700; font-size: 1rem; white-space: nowrap; box-shadow: 0 4px 15px rgba(var(--primary-rgb), 0.2);">Cari Buku</button>
        @if(!empty($search))
            <a href="{{ url('/recommendations'.(request('genre') ? '?genre='.urlencode(request('genre')) : '').(request('sort') ? '&sort='.request('sort') : '')) }}" class="btn btn-ghost" style="border-radius: 50px; padding: 12px; color: var(--danger);" title="Reset pencarian">✕</a>
        @endif
    </form>
</div>

{{-- ─── FILTER ROW (GENRES & SORT) ─────────────────────────── --}}
<div style="
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: var(--space-xl);
">
    <div style="display: flex; flex: 1; align-items: center; gap: 12px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: none;">
        {{-- Genre chips --}}
        <a href="{{ url('/recommendations'.(!empty($search) ? '?search='.$search : '').(request('sort') ? (empty($search)?'?':'&').'sort='.request('sort') : '')) }}"
           class="btn {{ !request('genre') ? 'btn-primary' : 'btn-outline' }}" style="border-radius: 50px; padding: 10px 24px; font-weight: 600; flex-shrink: 0; {{ !request('genre') ? '' : 'border-color: var(--border); color: var(--text-secondary); background: var(--bg-card);' }}">
            <span style="margin-right:6px;">⊞</span> Semua
        </a>
        
        @foreach($genres as $genre)
            @php
                $q = '?genre='.urlencode($genre);
                if (!empty($search)) $q .= '&search='.$search;
                if (request('sort')) $q .= '&sort='.request('sort');
                $isActive = request('genre') === $genre;
            @endphp
            <a href="{{ url('/recommendations'.$q) }}"
               class="btn {{ $isActive ? 'btn-primary' : 'btn-outline' }}" style="border-radius: 50px; padding: 10px 24px; font-weight: 600; color: {{ $isActive ? 'white' : 'var(--text-secondary)' }}; border-color: {{ $isActive ? 'var(--primary)' : 'var(--border)' }}; flex-shrink: 0; background: {{ $isActive ? 'var(--primary)' : 'var(--bg-card)' }};">
                <span style="margin-right:6px; font-size:1.1em; opacity: 0.8;">
                @if($genre == 'Romance') 🤍
                @elseif($genre == 'Fantasy') 🪄
                @elseif($genre == 'Mystery') 🔍
                @elseif($genre == 'Self Improvement') 📈
                @elseif($genre == 'Sci-Fi') 🚀
                @elseif($genre == 'Historical') 🏛️
                @else 📚
                @endif
                </span>
                {{ $genre }}
            </a>
        @endforeach
    </div>
    
    {{-- Sort Dropdown --}}
    <form method="GET" action="{{ url('/recommendations') }}" id="sortForm" style="display: flex; gap: 10px; align-items: center;">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if(request('genre'))
            <input type="hidden" name="genre" value="{{ request('genre') }}">
        @endif
        <select name="sort" class="form-select" style="border-radius: 50px; padding: 10px 16px; width: auto; border-color: var(--border); font-weight: 600; color: var(--primary); background: var(--bg-card);" onchange="document.getElementById('sortForm').submit()">
            <option value="rating" {{ ($sort ?? 'rating') === 'rating' ? 'selected' : '' }}>≢ Filter Lanjutan</option>
            <option value="terbaru" {{ ($sort ?? '') === 'terbaru' ? 'selected' : '' }}>🆕 Terbaru</option>
            <option value="judul" {{ ($sort ?? '') === 'judul' ? 'selected' : '' }}>🔤 Judul A-Z</option>
            <option value="ulasan" {{ ($sort ?? '') === 'ulasan' ? 'selected' : '' }}>💬 Ulasan</option>
        </select>
        @if(request('genre') || (request('sort') && request('sort') !== 'rating'))
            <a href="{{ url('/recommendations'.(!empty($search)?'?search='.$search:'')) }}" class="btn btn-ghost" style="border-radius: 50px; padding: 10px; color: var(--danger); background: var(--bg-card);" title="Reset filter">✕</a>
        @endif
    </form>
</div>

{{-- ─── AI REKOMENDASI PERSONAL ──────────────────────────── --}}
@auth
@if(isset($hasRecData) && $hasRecData && isset($recommendations) && $recommendations->count() > 0 && empty($search) && !request('genre'))
<div class="mb-xl">
    <div class="section-header">
        <div>
            <h2 style="font-size:1.05rem;font-weight:700;display:flex;align-items:center;gap:8px;">
                🤖 Khusus Untukmu
                @if(isset($topGenres) && $topGenres->count() > 0)
                    <span class="badge badge-primary" style="font-size:0.68rem;">{{ $topGenres[0]->genre }}</span>
                @endif
            </h2>
            <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Berdasarkan aktivitas membacamu</p>
        </div>
    </div>
    <div class="scroll-row" style="padding-bottom:var(--space-sm);gap:var(--space-md);">
        @foreach($recommendations as $book)
        @php $hasCover = $book->cover_buku && file_exists(public_path('uploads/covers/' . $book->cover_buku)); @endphp
        <a href="{{ url('/books/' . $book->id) }}" class="card book-card" style="width:140px;flex-shrink:0;">
            <div class="placeholder-img" style="aspect-ratio:2/3;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @if($hasCover)
                    <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}" alt="{{ $book->judul }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @else
                    <div class="book-no-cover">
                        <span class="no-cover-icon">📖</span>
                        <span class="no-cover-title">{{ $book->judul }}</span>
                        <span class="no-cover-author">{{ $book->penulis }}</span>
                    </div>
                @endif
            </div>
            <div style="padding:10px;">
                <div class="book-title" style="font-size:0.75rem;margin-bottom:3px;">{{ BookVerseHelper::truncate($book->judul, 40) }}</div>
                {!! BookVerseHelper::starRating((float)($book->avg_rating ?? 0), false) !!}
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
@endauth

{{-- ─── TOP RATED (hanya muncul saat tidak ada filter) ──── --}}
@if(isset($topRated) && $topRated->count() > 0 && empty($search) && !request('genre'))
<div class="mb-xl">
    <div class="section-header">
        <h2 style="font-size:1.05rem;font-weight:700;">🏆 Rating Tertinggi</h2>
    </div>
    <div class="scroll-row" style="padding-bottom:var(--space-sm);gap:var(--space-md);">
        @foreach($topRated as $book)
        @php $hasCover = $book->cover_buku && file_exists(public_path('uploads/covers/' . $book->cover_buku)); @endphp
        <a href="{{ url('/books/' . $book->id) }}" class="card book-card" style="width:140px;flex-shrink:0;">
            <div class="placeholder-img" style="aspect-ratio:2/3;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @if($hasCover)
                    <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}" alt="{{ $book->judul }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @else
                    <div class="book-no-cover">
                        <span class="no-cover-icon">📖</span>
                        <span class="no-cover-title">{{ $book->judul }}</span>
                        <span class="no-cover-author">{{ $book->penulis }}</span>
                    </div>
                @endif
            </div>
            <div style="padding:10px;">
                <div class="book-title" style="font-size:0.75rem;margin-bottom:3px;">{{ BookVerseHelper::truncate($book->judul, 40) }}</div>
                {!! BookVerseHelper::starRating((float)$book->avg_rating, false) !!}
                <div style="font-size:0.65rem;color:var(--text-muted);">{{ $book->total_rating }} ulasan</div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ─── SEMUA BUKU (GRID) ───────────────────────────────── --}}
<div class="section-header" style="margin-bottom:var(--space-md);">
    <h2 style="font-size:1.05rem;font-weight:700;">
        📚 {{ request('genre') ? request('genre') : 'Semua Buku' }}
        @if(!empty($search))
            <span class="badge badge-secondary" style="font-size:0.7rem;margin-left:6px;">hasil: "{{ $search }}"</span>
        @endif
    </h2>
    <span style="font-size:0.8rem;color:var(--text-muted);">{{ $books->count() }} buku</span>
</div>

@if($books->count() > 0)
    @php $perPage = 10; $total = $books->count(); @endphp
    <div class="grid-5 stagger-in" id="booksGrid" style="gap:var(--space-lg);">
        @foreach($books as $i => $book)
        @php $hasCover = $book->cover_buku && file_exists(public_path('uploads/covers/' . $book->cover_buku)); @endphp
        <a href="{{ url('/books/' . $book->id) }}" class="card book-card book-item"
           style="{{ $i >= $perPage ? 'display:none;' : '' }}">
            <div class="placeholder-img" style="aspect-ratio:2/3;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @if($hasCover)
                    <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}" alt="{{ $book->judul }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @else
                    <div class="book-no-cover" style="padding:16px;">
                        <span class="no-cover-icon">📖</span>
                        <span class="no-cover-title" style="-webkit-line-clamp:4;">{{ $book->judul }}</span>
                        <span class="no-cover-author">{{ $book->penulis }}</span>
                    </div>
                @endif
            </div>
            <div class="book-info">
                <span class="book-genre-tag">{{ $book->genre_buku }}</span>
                <div class="book-title">{{ $book->judul }}</div>
                <div class="book-author">{{ $book->penulis }}</div>
                {!! BookVerseHelper::starRating((float)($book->avg_rating ?? 0)) !!}
            </div>
        </a>
        @endforeach
    </div>

    {{-- Load More button --}}
    @if($total > $perPage)
    <div style="text-align:center;margin-top:var(--space-xl);" id="loadMoreWrap">
        <button onclick="loadMoreBooks()" id="loadMoreBtn"
                class="btn btn-outline"
                style="padding:12px 32px;font-size:0.9rem;border-radius:var(--radius-xl);">
            📚 Lihat Lainnya
            <span id="loadMoreCount" style="font-size:0.75rem;color:var(--text-muted);margin-left:6px;">
                ({{ $total - $perPage }} buku tersisa)
            </span>
        </button>
    </div>
    @endif

@else
    <div class="empty-state">
        <span class="emoji">📚</span>
        <h3>{{ !empty($search) ? 'Tidak ada hasil untuk "'.$search.'"' : 'Belum ada buku' }}</h3>
        <p>{{ !empty($search) ? 'Coba kata kunci atau genre lain.' : 'Buku akan segera ditambahkan.' }}</p>
        @if(!empty($search) || request('genre'))
            <a href="{{ url('/recommendations') }}" class="btn btn-primary" style="margin-top:var(--space-md);">Lihat Semua Buku</a>
        @endif
    </div>
@endif

<script>
(function() {
    var shown = {{ isset($perPage) ? $perPage : 10 }};
    var step  = 10;

    window.loadMoreBooks = function() {
        var items = document.querySelectorAll('.book-item[style*="display:none"]');
        var toShow = Array.from(items).slice(0, step);

        toShow.forEach(function(el, i) {
            setTimeout(function() {
                el.style.display = '';
                el.style.opacity = '0';
                el.style.transform = 'translateY(12px)';
                requestAnimationFrame(function() {
                    el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                });
            }, i * 40);
        });

        shown += toShow.length;
        var remaining = document.querySelectorAll('.book-item[style*="display:none"]').length;

        if (remaining <= 0) {
            var wrap = document.getElementById('loadMoreWrap');
            if (wrap) wrap.style.display = 'none';
        } else {
            var countEl = document.getElementById('loadMoreCount');
            if (countEl) countEl.textContent = '(' + remaining + ' buku tersisa)';
        }
    };
})();
</script>

@endsection
