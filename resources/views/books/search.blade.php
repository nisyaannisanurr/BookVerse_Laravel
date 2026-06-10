@extends('layouts.main')
@section('title', 'Hasil Pencarian "{{ $keyword }}" — BookVerse')

@php use App\Helpers\BookVerseHelper; @endphp

@section('content')

<div class="d-flex align-center gap-md mb-lg flex-wrap">
    <a href="{{ url('/books') }}" class="btn btn-ghost btn-sm">← Kembali</a>
    <h2 style="font-family:var(--font-display);font-size:1.4rem;">
        Hasil untuk <span style="color:var(--primary);">"{{ $keyword }}"</span>
    </h2>
    <span class="badge badge-secondary">{{ $books->count() }} buku</span>
</div>

<!-- Search Bar -->
<div style="max-width:540px;margin-bottom:var(--space-xl);">
    <form method="GET" action="{{ url('/search') }}" style="display:flex;gap:var(--space-sm);">
        <input type="text" name="search" class="form-input" value="{{ $keyword }}" placeholder="Cari buku lain...">
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
    </form>
</div>

@if($books->count() > 0)
    <div class="grid-5 stagger-in">
        @foreach($books as $book)
        <a href="{{ url('/books/' . $book->id) }}" class="card book-card">
            <div class="placeholder-img" style="aspect-ratio:2/3;font-size:2rem;">
                @if($book->cover_buku && file_exists(public_path('uploads/covers/' . $book->cover_buku)))
                    <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}"
                         alt="{{ $book->judul }}" class="book-cover">
                @else
                    📖
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
@else
    <div class="empty-state">
        <span class="emoji">🔍</span>
        <h3>Tidak ada hasil untuk "{{ $keyword }}"</h3>
        <p>Coba kata kunci lain atau jelajahi semua buku.</p>
        <a href="{{ url('/books') }}" class="btn btn-primary mt-md">Jelajahi Semua Buku</a>
    </div>
@endif
@endsection
