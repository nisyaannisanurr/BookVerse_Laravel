@extends('layouts.main')
@section('title', 'Donasi Buku — ' . $campaign->judul)
@section('content')

<div style="max-width: 620px; margin: 0 auto;">

    {{-- Breadcrumb --}}
    <div style="margin-bottom: 16px;" class="fade-in-up">
        <a href="{{ url('/donasi/' . $campaign->id) }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">← Kembali ke Kampanye</a>
    </div>

    {{-- Header --}}
    <div style="text-align: center; margin-bottom: 28px;" class="fade-in-up">
        <span style="font-size: 3.5rem; display: block; margin-bottom: 8px;">📦</span>
        <h1 style="font-family: var(--font-display); font-size: 1.8rem; font-weight: 800; color: var(--text); margin-bottom: 8px;">
            Donasikan Buku
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Untuk kampanye: <strong style="color: #d97706;">{{ $campaign->judul }}</strong>
        </p>
    </div>

    {{-- Form --}}
    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 24px; padding: 36px; margin-bottom: 32px;" class="fade-in-up">
        <form method="POST" action="{{ url('/donasi/' . $campaign->id . '/donate') }}">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                    Judul Buku <span style="color: var(--danger);">*</span>
                </label>
                <input type="text" name="judul_buku" value="{{ old('judul_buku') }}" placeholder="Contoh: Laskar Pelangi, Buku Cerita Anak, dll." required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none;">
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Jika lebih dari satu buku, bisa ditulis dipisah koma</div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                        Jumlah Buku <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" max="500" required
                           style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                        Kondisi Buku <span style="color: var(--danger);">*</span>
                    </label>
                    <select name="kondisi" required
                            style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none; appearance: auto;">
                        <option value="bekas_layak" {{ old('kondisi') === 'bekas_layak' ? 'selected' : '' }}>Bekas Layak Baca</option>
                        <option value="baru" {{ old('kondisi') === 'baru' ? 'selected' : '' }}>Baru</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                    Catatan (Opsional)
                </label>
                <textarea name="catatan" rows="3" placeholder="Catatan tambahan, misal: buku untuk kelas 4-6 SD..." 
                          style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none; resize: vertical;">{{ old('catatan') }}</textarea>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 6px; color: var(--text);">
                    Nomor WhatsApp Anda <span style="color: var(--danger);">*</span>
                </label>
                <input type="text" name="no_wa_donatur" value="{{ old('no_wa_donatur') }}" placeholder="Contoh: 08123456789" required
                       style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface); color: var(--text); outline: none;">
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Agar Mitra dapat menghubungi Anda dengan mudah.</div>
            </div>

            {{-- Alamat Pengiriman --}}
            @php $mitra = $campaign->user->mitraVerification ?? null; @endphp
            @if($mitra)
            <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 14px; padding: 16px; margin-bottom: 24px;">
                <div style="font-weight: 700; font-size: 0.88rem; color: #92400e; margin-bottom: 8px;">📍 Alamat Pengiriman Buku</div>
                <div style="font-size: 0.85rem; color: #78716c; line-height: 1.6;">
                    <strong>{{ $mitra->nama_instansi }}</strong><br>
                    {{ $mitra->alamat_lengkap }}<br>
                    a.n. {{ $mitra->nama_penanggung_jawab }} ({{ $mitra->no_telepon }})
                </div>
            </div>
            @endif

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; border-radius: 50px; font-weight: 700; font-size: 1.05rem; background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 8px 20px rgba(217, 119, 6, 0.3);">
                📨 Kirim Donasi
            </button>

            <p style="text-align: center; margin-top: 14px; font-size: 0.78rem; color: var(--text-muted);">
                Setelah donasi dicatat, Anda akan diarahkan untuk menghubungi Mitra via WhatsApp. Anda juga bisa melacak pengiriman di halaman Profil.
            </p>
        </form>
    </div>
</div>

@endsection
