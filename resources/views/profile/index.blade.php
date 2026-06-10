@extends('layouts.main')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<!-- Profile Header -->
<div class="card mb-lg fade-in-up">
    <div class="card-body text-center" style="padding:var(--space-xl);">
        @if($user->foto_profil)
            <img src="{{ BookVerseHelper::uploadUrl('profiles', $user->foto_profil) }}" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:var(--space-md);">
        @else
            <div class="placeholder-img" style="width:80px;height:80px;border-radius:50%;margin:0 auto var(--space-md);font-size:2rem;">👤</div>
        @endif
        <h2 style="font-family:var(--font-display);">{{ $user->username }}</h2>
        <p class="text-sm text-muted">{{ $user->email }}</p>
        @if($user->bio)
            <p class="text-sm text-secondary mt-sm">{{ $user->bio }}</p>
        @endif
        <a href="{{ url('/profile/edit') }}" class="btn btn-outline btn-sm mt-md">✏️ Edit Profil</a>
    </div>
</div>

<!-- Tabs -->
<div class="tabs mb-lg">
    <a href="{{ url('/profile?tab=shelf') }}" class="tab {{ ($tab ?? 'shelf') === 'shelf' ? 'active' : '' }}">📚 Rak Buku</a>
    <a href="{{ url('/profile?tab=sales') }}" class="tab {{ ($tab ?? '') === 'sales' ? 'active' : '' }}">🏷️ Jualanku</a>
</div>

@if(($tab ?? 'shelf') === 'shelf')
    <!-- Shelf Sub-tabs -->
    <div class="chip-group mb-md">
        <a href="{{ url('/profile?tab=shelf&shelf=sedang_dibaca') }}" class="chip {{ ($shelfTab ?? 'sedang_dibaca') === 'sedang_dibaca' ? 'active' : '' }}">📖 Sedang Dibaca ({{ $shelfCounts['sedang_dibaca'] }})</a>
        <a href="{{ url('/profile?tab=shelf&shelf=selesai') }}" class="chip {{ ($shelfTab ?? '') === 'selesai' ? 'active' : '' }}">✅ Selesai ({{ $shelfCounts['selesai'] }})</a>
        <a href="{{ url('/profile?tab=shelf&shelf=wishlist') }}" class="chip {{ ($shelfTab ?? '') === 'wishlist' ? 'active' : '' }}">❤️ Wishlist ({{ $shelfCounts['wishlist'] }})</a>
    </div>

    @if($shelfBooks->count() > 0)
        <div class="grid-4 stagger-in">
            @foreach($shelfBooks as $item)
                <a href="{{ url('/books/' . $item->buku_id) }}" class="card book-card">
                    <div class="placeholder-img" style="aspect-ratio:2/3;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                        @if($item->cover_buku && file_exists(public_path('uploads/covers/' . $item->cover_buku)))
                            <img src="{{ BookVerseHelper::uploadUrl('covers', $item->cover_buku) }}" alt="{{ $item->judul }}" class="card-img" style="aspect-ratio:2/3;">
                        @else
                            📖
                        @endif
                    </div>
                    <div class="card-body">
                        <span class="book-genre">{{ $item->genre_buku }}</span>
                        <div class="book-title">{{ $item->judul }}</div>
                        <div class="book-author">{{ $item->penulis }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <span class="emoji">📚</span>
            <h3>Rak kosong</h3>
            <p>Mulai tambahkan buku ke rak Anda!</p>
            <a href="{{ url('/books') }}" class="btn btn-primary mt-md">Jelajahi Buku</a>
        </div>
    @endif

@else
    <!-- Sales Tab -->
    @if($sales->count() > 0)
        <div class="grid-3 stagger-in">
            @foreach($sales as $item)
                <a href="{{ url('/preloved/' . $item->id) }}" class="card preloved-card">
                    <div class="placeholder-img" style="aspect-ratio:1/1;">
                        @if($item->foto_buku && file_exists(public_path('uploads/preloved/' . $item->foto_buku)))
                            <img src="{{ BookVerseHelper::uploadUrl('preloved', $item->foto_buku) }}" alt="{{ $item->judul_buku }}" class="card-img" style="aspect-ratio:1/1;">
                        @else
                            📖
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="price">{{ BookVerseHelper::formatRupiah($item->harga) }}</div>
                        <div class="book-title">{{ $item->judul_buku }}</div>
                        {!! BookVerseHelper::statusBadge($item->status_buku) !!}
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <span class="emoji">🏷️</span>
            <h3>Belum ada jualan</h3>
            <p>Jual buku preloved Anda di BookVerse!</p>
            <a href="{{ url('/preloved/create') }}" class="btn btn-accent mt-md">Jual Buku</a>
        </div>
    @endif
@endif
@endsection
