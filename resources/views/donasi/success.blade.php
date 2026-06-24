@extends('layouts.main')
@section('title', 'Donasi Berhasil — BookVerse')
@section('content')

@php
    $mitra = $campaign->user->mitraVerification ?? null;
    $noWaMitra = $mitra ? $mitra->no_telepon : '';
    
    // Format WA number for wa.me link
    $waFormatted = $noWaMitra;
    if (str_starts_with($waFormatted, '0')) {
        $waFormatted = '62' . substr($waFormatted, 1);
    }
    
    $pesanTemplate = "Halo, saya telah mencatat donasi buku di BookVerse untuk kampanye *{$campaign->judul}*.\n\nBuku: {$donasi->judul_buku} ({$donasi->jumlah} eksemplar)\n\nKapan saya bisa mengirimkannya/mengantarkannya?";
    $waLink = "https://wa.me/{$waFormatted}?text=" . urlencode($pesanTemplate);
@endphp

<div style="max-width: 620px; margin: 40px auto; text-align: center;" class="fade-in-up">
    
    <div style="font-size: 5rem; margin-bottom: 20px;">🎉</div>
    
    <h1 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; color: var(--text); margin-bottom: 12px;">
        Terima Kasih Atas Donasi Anda!
    </h1>
    
    <p style="color: #6b7280; font-size: 1.1rem; line-height: 1.6; margin-bottom: 30px;">
        Data donasi Anda untuk kampanye <strong>{{ $campaign->judul }}</strong> telah berhasil dicatat. 
        Langkah selanjutnya adalah menghubungi Mitra untuk mengatur pengiriman buku.
    </p>

    @if($mitra)
    <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 16px; padding: 24px; margin-bottom: 30px; text-align: left;">
        <h3 style="font-size: 1rem; font-weight: 700; color: #92400e; margin-bottom: 12px;">🏫 Informasi Mitra</h3>
        <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.95rem; color: #78716c; line-height: 1.8;">
            <li><strong>Nama:</strong> {{ $mitra->nama_instansi }}</li>
            <li><strong>Alamat:</strong> {{ $mitra->alamat_lengkap }}</li>
            <li><strong>Kontak:</strong> {{ $mitra->nama_penanggung_jawab }} ({{ $mitra->no_telepon }})</li>
        </ul>
    </div>

    <a href="{{ $waLink }}" target="_blank" class="btn btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 16px; border-radius: 50px; font-weight: 700; font-size: 1.1rem; background: #25D366; color: white; border: none; box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3); text-decoration: none; margin-bottom: 20px;">
        <span style="font-size: 1.3rem;">💬</span> Hubungi Mitra via WhatsApp
    </a>
    @endif

    <div style="margin-top: 30px; padding-top: 24px; border-top: 1px dashed var(--border);">
        <p style="font-size: 0.9rem; color: #6b7280; margin-bottom: 16px;">
            Atau, Anda sudah mengirimkannya via Kurir? Lacak dan masukkan nomor resi di halaman Profil Anda.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <a href="{{ url('/profile') }}" class="btn btn-outline" style="padding: 10px 24px; border-radius: 50px; font-weight: 600;">
                👤 Ke Profil Saya
            </a>
            <a href="{{ url('/donasi') }}" class="btn btn-outline" style="padding: 10px 24px; border-radius: 50px; font-weight: 600;">
                🔍 Cari Kampanye Lain
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var duration = 3 * 1000;
        var animationEnd = Date.now() + duration;
        var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            var particleCount = 50 * (timeLeft / duration);
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
        }, 250);
    });
</script>

@endsection
