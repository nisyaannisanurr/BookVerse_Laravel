@extends('layouts.main')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<h2 class="mb-md" style="font-family:var(--font-display);">✨ Rekomendasi Buku</h2>

<!-- Genre Filter Chips -->
<div class="chip-group">
    <a href="{{ url('/books?genre=Semua') }}" class="chip {{ ($genre ?? 'Semua') === 'Semua' ? 'active' : '' }}">Semua</a>
    @foreach($genres as $g)
        <a href="{{ url('/books?genre=' . urlencode($g)) }}" class="chip {{ ($genre ?? '') === $g ? 'active' : '' }}">
            {{ $g }}
        </a>
    @endforeach
</div>

<!-- AI Recommendations -->
@auth
@if($hasRecData && $recommendations->count() > 0)
<div class="mb-lg">
    <div class="section-title"><span class="emoji">🤖</span> Karena Kamu Menyukai
        @if($topGenres->count() > 0)
            <span class="badge badge-primary">{{ $topGenres[0]->genre }}</span>
        @endif
    </div>
    <div class="scroll-row stagger-in">
        @foreach($recommendations as $book)
            <a href="{{ url('/books/' . $book->id) }}" class="card book-card scroll-card" style="width:160px;">
                <div class="placeholder-img" style="aspect-ratio:2/3;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                    @if($book->cover_buku && file_exists(public_path('uploads/covers/' . $book->cover_buku)))
                        <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}" alt="{{ $book->judul }}" class="card-img" style="aspect-ratio:2/3;">
                    @else
                        📖
                    @endif
                </div>
                <div class="card-body" style="padding:10px;">
                    <div class="book-title" style="font-size:0.8rem;">{{ $book->judul }}</div>
                    <div class="book-author" style="font-size:0.7rem;">{{ $book->penulis }}</div>
                    {!! BookVerseHelper::starRating((float)($book->avg_rating ?? 0), false) !!}
                </div>
            </a>
        @endforeach
    </div>
</div>
@elseif(!$hasRecData)
<div class="card mb-lg" style="border:2px dashed var(--border);">
    <div class="card-body text-center" style="padding:var(--space-xl);">
        <div style="font-size:2.5rem;margin-bottom:var(--space-md);">🤖</div>
        <h3 style="margin-bottom:var(--space-sm);">Rekomendasi AI</h3>
        <p class="text-secondary text-sm">Mulai jelajahi buku, beri rating, dan tambahkan ke wishlist untuk mendapatkan rekomendasi yang dipersonalisasi untukmu!</p>
    </div>
</div>
@endif
@endauth

<!-- Top Rated -->
@if($topRated->count() > 0)
<div class="section-title"><span class="emoji">🏆</span> Rating Tertinggi</div>
<div class="scroll-row stagger-in mb-lg">
    @foreach($topRated as $book)
        <a href="{{ url('/books/' . $book->id) }}" class="card book-card scroll-card" style="width:160px;">
            <div class="placeholder-img" style="aspect-ratio:2/3;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                @if($book->cover_buku && file_exists(public_path('uploads/covers/' . $book->cover_buku)))
                    <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}" alt="{{ $book->judul }}" class="card-img" style="aspect-ratio:2/3;">
                @else
                    📖
                @endif
            </div>
            <div class="card-body" style="padding:10px;">
                <div class="book-title" style="font-size:0.8rem;">{{ $book->judul }}</div>
                <div class="book-author" style="font-size:0.7rem;">{{ $book->penulis }}</div>
                {!! BookVerseHelper::starRating((float)$book->avg_rating) !!}
                <div class="text-xs text-muted mt-sm">{{ $book->total_rating }} ulasan</div>
            </div>
        </a>
    @endforeach
</div>
@endif

<!-- All Books Grid -->
<div class="section-title"><span class="emoji">📖</span> Semua Buku</div>
@if($books->count() > 0)
    <div class="grid-4 stagger-in">
        @foreach($books as $book)
            <a href="{{ url('/books/' . $book->id) }}" class="card book-card">
                <div class="placeholder-img" style="aspect-ratio:2/3;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                    @if($book->cover_buku && file_exists(public_path('uploads/covers/' . $book->cover_buku)))
                        <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}" alt="{{ $book->judul }}" class="card-img" style="aspect-ratio:2/3;">
                    @else
                        📖
                    @endif
                </div>
                <div class="card-body">
                    <span class="book-genre">{{ $book->genre_buku }}</span>
                    <div class="book-title">{{ $book->judul }}</div>
                    <div class="book-author">{{ $book->penulis }}</div>
                    {!! BookVerseHelper::starRating((float)$book->avg_rating) !!}
                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <span class="emoji">📚</span>
        <h3>Belum ada buku</h3>
        <p>Buku akan segera ditambahkan oleh admin.</p>
    </div>
@endif
@endsection
