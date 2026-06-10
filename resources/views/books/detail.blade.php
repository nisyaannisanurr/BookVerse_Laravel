@extends('layouts.main')
@section('title', $book->judul . ' — BookVerse')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

{{-- ─── HERO: Cover + Info ──────────────────────────────── --}}
<div class="fade-in-up" style="
    background: linear-gradient(135deg, var(--primary-light) 0%, #f8f6ff 50%, var(--bg-white) 100%);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    display: grid;
    grid-template-columns: 160px 1fr;
    gap: var(--space-xl);
    align-items: start;
    margin-bottom: var(--space-lg);
    position: relative;
    overflow: hidden;
">
    {{-- Decorative circle --}}
    <div style="position:absolute;top:-60px;right:-60px;width:220px;height:220px;background:rgba(79,60,201,0.06);border-radius:50%;pointer-events:none;"></div>

    {{-- Cover --}}
    <div style="position:relative;z-index:1;">
        @php $hasCover = $book->cover_buku && file_exists(public_path('uploads/covers/' . $book->cover_buku)); @endphp
        @if($hasCover)
            <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}"
                 alt="{{ $book->judul }}"
                 style="width:160px;height:240px;object-fit:cover;border-radius:var(--radius-lg);box-shadow:0 8px 32px rgba(79,60,201,0.25);display:block;">
        @else
            <div style="
                width:160px;height:240px;
                background:linear-gradient(145deg,#ede9fb,#c4b5fd 60%,#a78bfa);
                border-radius:var(--radius-lg);
                box-shadow:0 8px 32px rgba(79,60,201,0.25);
                display:flex;flex-direction:column;
                align-items:center;justify-content:center;
                gap:10px;padding:16px;text-align:center;
            ">
                <span style="font-size:2.5rem;opacity:0.8;">📖</span>
                <span style="font-size:0.72rem;font-weight:700;color:#3730a3;line-height:1.4;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;">{{ $book->judul }}</span>
                <span style="font-size:0.63rem;color:#5b21b6;opacity:0.8;">{{ $book->penulis }}</span>
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div style="position:relative;z-index:1;">
        <span class="book-genre-tag" style="margin-bottom:var(--space-sm);">{{ $book->genre_buku }}</span>
        <h1 class="book-detail-title" style="font-size:1.6rem;margin-top:var(--space-sm);">{{ $book->judul }}</h1>
        <p class="book-detail-author">oleh <strong>{{ $book->penulis }}</strong></p>

        <div style="display:flex;align-items:center;gap:var(--space-lg);margin-bottom:var(--space-lg);flex-wrap:wrap;">
            <div>
                {!! BookVerseHelper::starRating((float)$book->avg_rating) !!}
                <div style="font-size:0.78rem;color:var(--text-muted);margin-top:3px;">{{ $book->total_rating }} ulasan</div>
            </div>
        </div>

        @auth
        <div class="book-detail-actions">
            @if($shelfEntry)
                <span class="badge badge-info" style="padding:8px 16px;font-size:0.85rem;">
                    📚 {!! BookVerseHelper::statusBadge($shelfEntry->status) !!}
                </span>
                <form method="POST" action="{{ url('/books/shelf/remove') }}" class="inline-form">
                    @csrf
                    <input type="hidden" name="buku_id" value="{{ $book->id }}">
                    <input type="hidden" name="redirect" value="{{ url('/books/' . $book->id) }}">
                    <button type="submit" class="btn btn-ghost btn-sm">Hapus dari Rak</button>
                </form>
            @else
                <form method="POST" action="{{ url('/books/shelf') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="buku_id" value="{{ $book->id }}">
                    <input type="hidden" name="status" value="wishlist">
                    <button type="submit" class="btn btn-accent btn-sm">❤️ Wishlist</button>
                </form>
                <form method="POST" action="{{ url('/books/shelf') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="buku_id" value="{{ $book->id }}">
                    <input type="hidden" name="status" value="sedang_dibaca">
                    <button type="submit" class="btn btn-outline btn-sm">📖 Sedang Dibaca</button>
                </form>
                <form method="POST" action="{{ url('/books/shelf') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="buku_id" value="{{ $book->id }}">
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" class="btn btn-ghost btn-sm">✅ Selesai</button>
                </form>
            @endif
        </div>
        @endauth
    </div>
</div>

<!-- Sinopsis -->
<div class="sinopsis-section fade-in-up">
    <h3 class="mb-md">Sinopsis</h3>
    <div class="sinopsis-text collapsed">{!! nl2br(e($book->sinopsis)) !!}</div>
    <button class="sinopsis-toggle">Baca Selengkapnya</button>
</div>

<!-- Rating Form (logged in only) -->
@auth
<div class="card mb-lg fade-in-up">
    <div class="card-body">
        <h3 class="mb-md">{{ $userRating ? '✏️ Edit Ulasan Anda' : '⭐ Beri Rating' }}</h3>
        <form method="POST" action="{{ url('/books/rate') }}">
            @csrf
            <input type="hidden" name="buku_id" value="{{ $book->id }}">

            <div class="form-group">
                <div class="star-input">
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" name="skor_rating" id="star{{ $i }}" value="{{ $i }}"
                               {{ ($userRating && (int)$userRating->skor_rating === $i) ? 'checked' : '' }} required>
                        <label for="star{{ $i }}">{{ ($userRating && (int)$userRating->skor_rating >= $i) ? '★' : '☆' }}</label>
                    @endfor
                </div>
            </div>

            <div class="form-group">
                <textarea name="ulasan_teks" class="form-textarea" placeholder="Tulis ulasan Anda tentang buku ini... (opsional)" rows="3">{{ $userRating->ulasan_teks ?? '' }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $userRating ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
            </button>
        </form>
    </div>
</div>
@endauth

<!-- Reviews Feed -->
<div class="section-title"><span class="emoji">💬</span> Ulasan Pembaca ({{ $reviews->count() }})</div>

@if($reviews->count() > 0)
    @foreach($reviews as $review)
        <div class="post-card fade-in-up">
            <div class="post-header">
                @if($review->user && $review->user->foto_profil)
                    <img src="{{ BookVerseHelper::uploadUrl('profiles', $review->user->foto_profil) }}" alt="" class="post-avatar">
                @else
                    <div class="post-avatar placeholder-img" style="border-radius:50%;font-size:1rem;">👤</div>
                @endif
                <div class="post-meta">
                    <div class="username">{{ $review->user->username ?? 'Unknown' }}</div>
                    <div class="time">{{ BookVerseHelper::timeAgo($review->tanggal_rating) }}</div>
                </div>
                <div>{!! BookVerseHelper::starRating((int)$review->skor_rating, false) !!}</div>
            </div>
            @if($review->ulasan_teks)
                <div class="post-content">{!! nl2br(e($review->ulasan_teks)) !!}</div>
            @endif
        </div>
    @endforeach
@else
    <div class="empty-state" style="padding:var(--space-xl) 0;">
        <span class="emoji">📝</span>
        <h3>Belum ada ulasan</h3>
        <p>Jadilah yang pertama memberikan ulasan untuk buku ini!</p>
    </div>
@endif

{{-- ─── BUKU SERUPA ──────────────────────────────────── --}}
@if($similarBooks->count() > 0)
<div style="margin-top:var(--space-xl);border-top:1px solid var(--border);padding-top:var(--space-xl);">
    <div class="section-header" style="margin-bottom:var(--space-md);">
        <h2 style="font-size:1.05rem;font-weight:700;">📚 Buku Serupa — {{ $book->genre_buku }}</h2>
        <a href="{{ url('/recommendations?genre='.urlencode($book->genre_buku)) }}" class="view-all">Lihat Semua →</a>
    </div>
    <div class="scroll-row" style="gap:var(--space-md);padding-bottom:var(--space-sm);">
        @foreach($similarBooks as $sim)
        @php $hasCover = $sim->cover_buku && file_exists(public_path('uploads/covers/' . $sim->cover_buku)); @endphp
        <a href="{{ url('/books/' . $sim->id) }}" class="card book-card" style="width:140px;flex-shrink:0;">
            <div class="placeholder-img" style="aspect-ratio:2/3;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @if($hasCover)
                    <img src="{{ BookVerseHelper::uploadUrl('covers', $sim->cover_buku) }}" alt="{{ $sim->judul }}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @else
                    <div class="book-no-cover">
                        <span class="no-cover-icon">📖</span>
                        <span class="no-cover-title">{{ $sim->judul }}</span>
                        <span class="no-cover-author">{{ $sim->penulis }}</span>
                    </div>
                @endif
            </div>
            <div style="padding:10px;">
                <div class="book-title" style="font-size:0.75rem;margin-bottom:3px;">{{ BookVerseHelper::truncate($sim->judul, 40) }}</div>
                <div class="book-author" style="font-size:0.68rem;">{{ $sim->penulis }}</div>
                {!! BookVerseHelper::starRating((float)($sim->avg_rating ?? 0), false) !!}
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection
