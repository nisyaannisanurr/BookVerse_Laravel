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
    <a href="{{ url('/profile?tab=donations') }}" class="tab {{ ($tab ?? '') === 'donations' ? 'active' : '' }}">📦 Donasi Saya</a>
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

@elseif(($tab ?? '') === 'sales')
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

@elseif(($tab ?? '') === 'donations')
    <!-- Donations Tab -->
    @if($donations->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 16px;" class="stagger-in">
            @foreach($donations as $donasi)
                @php 
                    $mitra = $donasi->campaign->user->mitraVerification ?? null;
                    $waFormatted = $mitra ? $mitra->no_telepon : '';
                    if (str_starts_with($waFormatted, '0')) $waFormatted = '62' . substr($waFormatted, 1);
                    $waLink = "https://wa.me/{$waFormatted}?text=" . urlencode("Halo, saya ingin menanyakan tentang pengiriman donasi buku '{$donasi->judul_buku}' untuk kampanye {$donasi->campaign->judul}.");
                @endphp
                <div class="card" style="padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-family: var(--font-display); font-size: 1.2rem; font-weight: 700; color: #1a1030; margin-bottom: 4px;">{{ $donasi->judul_buku }}</h3>
                            <p style="font-size: 0.85rem; color: #6b7280;">{{ $donasi->jumlah }} buku · Kondisi: {{ $donasi->kondisi === 'baru' ? 'Baru' : 'Bekas Layak' }}</p>
                            <p style="font-size: 0.85rem; color: #d97706; margin-top: 4px; font-weight: 600;">Kampanye: {{ $donasi->campaign->judul }} (🏫 {{ $mitra->nama_instansi ?? 'Mitra' }})</p>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: inline-block; padding: 6px 16px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; background: {{ $donasi->status_color }}15; color: {{ $donasi->status_color }};">
                                {{ $donasi->status_label }}
                            </span>
                            <div style="font-size: 0.75rem; color: #9b90a8; margin-top: 6px;">{{ $donasi->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    
                    <div style="background: #f8f5ff; border-radius: 12px; padding: 16px;">
                        @if($donasi->status_pengiriman === 'menunggu_dikirim')
                            <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 12px;">Buku sudah siap dikirim via kurir? Masukkan nomor resi pelacakan Anda di bawah ini untuk memperbarui status.</p>
                            <form method="POST" action="{{ url('/donasi/update-resi') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
                                @csrf
                                <input type="hidden" name="donasi_id" value="{{ $donasi->id }}">
                                <input type="text" name="resi_pengiriman" placeholder="Masukkan Nomor Resi" required style="flex: 1; min-width: 200px; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 0.9rem;">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px; font-weight: 600;">🚚 Set Dikirim</button>
                            </form>
                            @if($mitra)
                            <div style="margin-top: 12px; text-align: right;">
                                <a href="{{ $waLink }}" target="_blank" style="font-size: 0.8rem; color: #25D366; font-weight: 600; text-decoration: none;">💬 Atau atur pengiriman via WhatsApp</a>
                            </div>
                            @endif
                        @else
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 12px;">
                                <div>
                                    <span style="font-size: 0.75rem; color: #9b90a8; text-transform: uppercase; font-weight: 700;">Nomor Resi:</span>
                                    <div style="font-size: 1rem; font-family: monospace; font-weight: 700; color: #1a1030; margin-top: 4px;">{{ $donasi->resi_pengiriman ?? 'Tidak ada resi (Dikirim via WA/Langsung)' }}</div>
                                </div>
                                @if($mitra && $donasi->status_pengiriman !== 'diterima')
                                <a href="{{ $waLink }}" target="_blank" class="btn btn-sm" style="background: #25D366; color: white; border: none; font-weight: 600;">💬 Chat Mitra (WA)</a>
                                @endif
                            </div>
                            
                            @if($donasi->status_pengiriman === 'diterima')
                                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border);">
                                    <p style="font-size: 0.9rem; color: #16a34a; font-weight: 700; margin-bottom: 12px;">✅ Buku telah diterima oleh mitra!</p>
                                    
                                    @if($donasi->foto_terima || $donasi->pesan_terima)
                                        <div style="background: white; border-radius: 12px; padding: 16px; border: 1px solid var(--border);">
                                            <h4 style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px; font-weight: 700;">💌 Pesan & Bukti dari Mitra</h4>
                                            
                                            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                                                @if($donasi->foto_terima)
                                                    <div style="flex-shrink: 0;">
                                                        <img src="{{ BookVerseHelper::uploadUrl('donasi_bukti', $donasi->foto_terima) }}" alt="Bukti Terima" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border);">
                                                    </div>
                                                @endif
                                                
                                                @if($donasi->pesan_terima)
                                                    <div style="flex: 1; min-width: 200px;">
                                                        <div style="font-style: italic; font-size: 0.9rem; color: #4b5563; line-height: 1.5; padding: 10px; background: #f9fafb; border-radius: 8px; border-left: 3px solid var(--primary);">
                                                            "{{ $donasi->pesan_terima }}"
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <span class="emoji">📦</span>
            <h3>Belum ada donasi</h3>
            <p>Anda belum pernah mendonasikan buku.</p>
            <a href="{{ url('/donasi') }}" class="btn btn-primary mt-md">Mulai Berdonasi</a>
        </div>
    @endif
@endif
@endsection
