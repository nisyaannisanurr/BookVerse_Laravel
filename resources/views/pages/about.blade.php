@extends('layouts.main')
@section('title', 'Tentang Kami — BookVerse')

@section('content')

<div style="max-width:800px;margin:0 auto;">

    <!-- Hero -->
    <div style="text-align:center;padding:var(--space-xl) 0 var(--space-2xl);">
        <img src="{{ asset('images/logo-bookverse.png') }}" alt="BookVerse" style="width:100px;height:100px;object-fit:contain;margin-bottom:var(--space-lg);filter:drop-shadow(0 8px 20px rgba(79,60,201,0.25));">
        <h1 style="font-family:var(--font-display);font-size:2.5rem;font-weight:700;margin-bottom:var(--space-sm);">
            Tentang <span style="color:var(--primary);">BookVerse</span>
        </h1>
        <p style="font-size:1rem;color:var(--text-secondary);max-width:520px;margin:0 auto;line-height:1.8;">
            Platform komunitas buku terbaik di Indonesia — tempat para pecinta buku berkumpul,
            berbagi, dan menemukan dunia baru bersama.
        </p>
    </div>

    <!-- Divider -->
    <div style="display:flex;align-items:center;gap:var(--space-md);margin-bottom:var(--space-xl);">
        <div style="flex:1;height:1px;background:var(--border);"></div>
        <span style="color:var(--accent);font-size:1.2rem;">✦</span>
        <div style="flex:1;height:1px;background:var(--border);"></div>
    </div>

    <!-- Mission -->
    <div class="card" style="margin-bottom:var(--space-lg);background:linear-gradient(135deg,var(--primary-light),#fff);">
        <div class="card-body" style="padding:var(--space-xl);">
            <h2 style="font-family:var(--font-display);font-size:1.4rem;margin-bottom:var(--space-md);color:var(--primary);">🎯 Misi Kami</h2>
            <p style="color:var(--text-secondary);line-height:1.9;font-size:0.95rem;">
                BookVerse hadir untuk membangun komunitas membaca yang inklusif dan positif di Indonesia.
                Kami percaya bahwa setiap buku membuka jendela dunia baru, dan setiap pembaca berhak menemukan
                buku yang tepat untuk mereka. Melalui teknologi, kami menghubungkan jutaan pembaca dengan
                ribuan buku pilihan.
            </p>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="grid-2" style="margin-bottom:var(--space-xl);">
        @foreach([
            ['📚','Katalog Buku','Ribuan buku dari berbagai genre — fiksi, non-fiksi, sastra, sains, dan lainnya. Selalu diperbarui setiap minggu.'],
            ['🏘️','Komunitas','Bergabung dengan komunitas sesuai minatmu. Diskusi, review, dan berbagi pengalaman membaca bersama.'],
            ['⭐','Sistem Rating','Beri rating dan ulasan jujur untuk membantu pembaca lain menemukan buku terbaik.'],
            ['🏷️','Preloved Books','Jual dan beli buku bekas berkualitas. Hemat lebih banyak, baca lebih banyak!'],
        ] as [$icon, $title, $desc])
        <div class="card">
            <div class="card-body">
                <div style="font-size:1.8rem;margin-bottom:var(--space-sm);">{{ $icon }}</div>
                <h3 style="font-weight:700;margin-bottom:6px;font-size:1rem;">{{ $title }}</h3>
                <p style="font-size:0.85rem;color:var(--text-secondary);line-height:1.7;">{{ $desc }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Stats -->
    <div style="background:var(--text);border-radius:var(--radius-xl);padding:var(--space-xl);margin-bottom:var(--space-xl);display:grid;grid-template-columns:repeat(4,1fr);text-align:center;gap:var(--space-md);">
        @foreach([['10K+','Anggota'],['25K+','Buku'],['50+','Komunitas'],['15K+','Review']] as [$num, $label])
        <div>
            <div style="font-size:1.6rem;font-weight:700;color:white;">{{ $num }}</div>
            <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:3px;">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    <!-- CTA -->
    <div style="text-align:center;padding:var(--space-lg) 0 var(--space-xl);">
        <h2 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:var(--space-md);">Siap bergabung?</h2>
        <div class="d-flex gap-md justify-center">
            <a href="{{ url('/register') }}" class="btn btn-primary btn-lg">✍️ Daftar Gratis</a>
            <a href="{{ url('/books') }}" class="btn btn-outline btn-lg">📚 Jelajahi Buku</a>
        </div>
    </div>

</div>
@endsection
