@extends('layouts.main')
@section('title', $campaign->judul . ' — Donasi Buku BookVerse')
@section('content')

@php $mitra = $campaign->user->mitraVerification ?? null; @endphp

<style>
    .detail-hero {
        background: var(--gradient-light);
        border-radius: 24px;
        padding: 0 50px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        min-height: 220px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.03);
    }
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 28px;
        align-items: start;
    }
    .detail-main { display: flex; flex-direction: column; gap: 24px; }
    .detail-sidebar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 24px;
        position: sticky;
        top: 90px;
    }
    .mitra-info-card {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
    }
    .donation-list-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }
    .donation-list-item:last-child { border-bottom: none; }
    .donation-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    @media (max-width: 768px) {
        .detail-hero { padding: 30px 20px; min-height: 180px; }
        .detail-grid { grid-template-columns: 1fr; }
    }
</style>

{{-- ─── BREADCRUMB ─────────────────────────────── --}}
<div style="margin-bottom: 16px;" class="fade-in-up">
    <a href="{{ url('/donasi') }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">← Kembali ke Daftar Kampanye</a>
</div>

{{-- ─── HERO ──────────────────────────────────────── --}}
<div class="detail-hero fade-in-up">
    <div style="position: relative; z-index: 2; max-width: 600px; padding: 40px 0;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
            @if($mitra)
                <span style="background: rgba(79,70,229,0.15); color: var(--primary); padding: 6px 16px; border-radius: 50px; font-size: 0.8rem; font-weight: 700;">{{ $mitra->kategori }}</span>
            @endif
            @if($campaign->status === 'completed')
                <span style="background: rgba(22,163,74,0.15); color: #16a34a; padding: 6px 16px; border-radius: 50px; font-size: 0.8rem; font-weight: 700;">✅ Selesai</span>
            @endif
        </div>
        <h1 style="font-family: var(--font-display); font-size: 2.2rem; font-weight: 800; color: var(--text); margin-bottom: 12px; line-height: 1.3;">
            {{ $campaign->judul }}
        </h1>
        @if($mitra)
            <p style="color: #57534e; font-size: 1.05rem; display: flex; align-items: center; gap: 6px;">
                🏫 {{ $mitra->nama_instansi }} · {{ $mitra->alamat_lengkap }}
            </p>
        @endif
    </div>

    <div style="position: absolute; right: 0; bottom: 0; height: 100%; width: 50%; max-width: 500px; display: flex; justify-content: flex-end;">
        @if($campaign->foto_campaign && file_exists(public_path('uploads/campaigns/' . $campaign->foto_campaign)))
            <img src="{{ asset('uploads/campaigns/' . $campaign->foto_campaign) }}" style="height: 100%; width: 100%; object-fit: cover; object-position: right center; mix-blend-mode: multiply; -webkit-mask-image: linear-gradient(to right, transparent 0%, black 40%); mask-image: linear-gradient(to right, transparent 0%, black 40%);" alt="">
        @else
            <div style="font-size: 12rem; opacity: 0.08; line-height: 1; align-self: flex-end; margin-right: -20px; margin-bottom: -20px; pointer-events: none;">📦</div>
        @endif
    </div>
</div>

<div class="detail-grid">
    {{-- ─── MAIN CONTENT ──────────────────────── --}}
    <div class="detail-main">
        {{-- Progress --}}
        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 24px;" class="fade-in-up">
            <h3 style="font-weight: 700; margin-bottom: 16px; font-size: 1.1rem;">📊 Progress Donasi</h3>
            <div style="background: #f3f4f6; border-radius: 50px; height: 16px; overflow: hidden; margin-bottom: 12px;">
                <div style="height: 100%; border-radius: 50px; background: linear-gradient(90deg, var(--primary-light), var(--primary)); width: {{ $campaign->progress_percent }}%; transition: width 0.6s ease;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                <span style="color: var(--text-muted);"><strong style="color: var(--primary); font-size: 1.3rem;">{{ $campaign->terkumpul }}</strong> / {{ $campaign->target_buku }} buku terkumpul</span>
                <span style="font-weight: 700; color: var(--primary); font-size: 1.2rem;">{{ $campaign->progress_percent }}%</span>
            </div>
            <div style="display: flex; gap: 20px; margin-top: 16px; font-size: 0.85rem; color: var(--text-muted);">
                <span>⏰ Batas waktu: <strong>{{ $campaign->batas_waktu->format('d M Y') }}</strong></span>
                <span>📅 {{ $campaign->sisaHari() }} hari lagi</span>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 24px;" class="fade-in-up">
            <h3 style="font-weight: 700; margin-bottom: 12px; font-size: 1.1rem;">📝 Tentang Kampanye</h3>
            <p style="color: var(--text-secondary); line-height: 1.8; white-space: pre-line;">{{ $campaign->deskripsi }}</p>
        </div>

        {{-- Info Mitra --}}
        @if($mitra)
        <div class="mitra-info-card fade-in-up">
            <h3 style="font-weight: 700; margin-bottom: 16px; font-size: 1.1rem;">🏫 Tentang Instansi Penerima</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 4px;">Nama Instansi</div>
                    <div style="font-weight: 600;">{{ $mitra->nama_instansi }}</div>
                </div>
                <div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 4px;">Kategori</div>
                    <div style="font-weight: 600;">{{ $mitra->kategori }}</div>
                </div>
                <div style="grid-column: span 2;">
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 4px;">Alamat</div>
                    <div style="font-weight: 600;">{{ $mitra->alamat_lengkap }}</div>
                </div>
                <div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 4px;">Penanggung Jawab</div>
                    <div style="font-weight: 600;">{{ $mitra->nama_penanggung_jawab }}</div>
                </div>
                <div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 4px;">Telepon</div>
                    <div style="font-weight: 600;">{{ $mitra->no_telepon }}</div>
                </div>
            </div>
            @if($mitra->link_maps)
                <a href="{{ $mitra->link_maps }}" target="_blank" style="display: inline-block; margin-top: 14px; color: #2563eb; font-size: 0.85rem; font-weight: 600; text-decoration: none;">
                    📍 Lihat di Google Maps →
                </a>
            @endif
        </div>
        @endif

        {{-- Recent Donations --}}
        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 24px;" class="fade-in-up">
            <h3 style="font-weight: 700; margin-bottom: 12px; font-size: 1.1rem;">❤️ Donatur Terbaru</h3>
            @if($recentDonations->count() > 0)
                @foreach($recentDonations as $donasi)
                    <div class="donation-list-item">
                        <div class="donation-avatar">👤</div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; font-size: 0.9rem;">{{ $donasi->user->username ?? 'Anonim' }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">
                                {{ $donasi->judul_buku }} · {{ $donasi->jumlah }} eksemplar · {{ $donasi->kondisi === 'baru' ? 'Baru' : 'Bekas Layak' }}
                            </div>
                        </div>
                        <span class="status-badge" style="background: {{ $donasi->status_color }}15; color: {{ $donasi->status_color }};">
                            {{ $donasi->status_label }}
                        </span>
                    </div>
                @endforeach
            @else
                <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; padding: 20px;">Belum ada donatur. Jadilah yang pertama! 🎉</p>
            @endif
        </div>
    </div>

    {{-- ─── SIDEBAR ───────────────────────────── --}}
    <div class="detail-sidebar fade-in-up">
        @if($campaign->isActive() && $campaign->sisaHari() > 0)
            <h3 style="font-weight: 700; margin-bottom: 16px; font-size: 1.15rem; text-align: center;">Donasi Sekarang</h3>
            @auth
                <a href="{{ url('/donasi/' . $campaign->id . '/donate') }}" class="btn btn-primary" style="width: 100%; padding: 14px; border-radius: 50px; font-weight: 700; font-size: 1.05rem; background: linear-gradient(135deg, var(--primary-light), var(--primary)); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3); text-align: center; display: block;">
                    📦 Donasikan Buku
                </a>
            @else
                <a href="{{ url('/login') }}" class="btn btn-primary" style="width: 100%; padding: 14px; border-radius: 50px; font-weight: 700; font-size: 1.05rem; background: linear-gradient(135deg, var(--primary-light), var(--primary)); text-align: center; display: block;">
                    Masuk untuk Donasi
                </a>
            @endauth
        @else
            <div style="text-align: center; padding: 20px; color: var(--text-muted);">
                <span style="font-size: 3rem; display: block; margin-bottom: 10px;">✅</span>
                <p style="font-weight: 600;">Kampanye ini telah selesai</p>
            </div>
        @endif

        {{-- Foto Instansi --}}
        @if($mitra && $mitra->foto_bangunan)
            <div style="margin-top: 20px; border-radius: 16px; overflow: hidden; border: 1px solid var(--border);">
                <img src="{{ asset('uploads/mitra/' . $mitra->foto_bangunan) }}" alt="Foto instansi" style="width: 100%; height: 180px; object-fit: cover;">
                <div style="padding: 10px; text-align: center; font-size: 0.78rem; color: var(--text-muted);">Foto {{ $mitra->nama_instansi }}</div>
            </div>
        @endif

        {{-- Share --}}
        <div style="margin-top: 20px; text-align: center;">
            <button onclick="navigator.clipboard.writeText('{{ url('/donasi/' . $campaign->id) }}'); alert('Tautan kampanye berhasil disalin!')"
               style="width: 100%; padding: 12px; border-radius: 50px; background: var(--bg-surface); border: 2px dashed var(--primary-light); color: var(--primary); font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 16px; font-size: 0.95rem; transition: all 0.3s;">
               🔗 Salin Tautan Kampanye
            </button>
            
            <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px;">Atau bagikan ke media sosial:</p>
            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                @php
                    $waText = "Mari ikut berdonasi buku untuk kampanye *" . $campaign->judul . "* di BookVerse!\n\nKlik tautan di bawah ini untuk berdonasi:\n" . url('/donasi/' . $campaign->id);
                @endphp
                <!-- WhatsApp -->
                <a href="https://wa.me/?text={{ rawurlencode($waText) }}" target="_blank"
                   style="width: 40px; height: 40px; border-radius: 50%; background: #25D366; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; text-decoration: none;" title="Bagikan ke WhatsApp">
                   💬
                </a>
                <!-- X (Twitter) -->
                <a href="https://twitter.com/intent/tweet?text={{ urlencode('Yuk ikut donasi buku untuk ' . $campaign->judul . ' di BookVerse!') }}&url={{ urlencode(url('/donasi/' . $campaign->id)) }}" target="_blank"
                   style="width: 40px; height: 40px; border-radius: 50%; background: #000000; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; text-decoration: none; font-weight: 800;" title="Bagikan ke X">
                   𝕏
                </a>
                <!-- Telegram -->
                <a href="https://t.me/share/url?url={{ urlencode(url('/donasi/' . $campaign->id)) }}&text={{ urlencode($campaign->judul) }}" target="_blank"
                   style="width: 40px; height: 40px; border-radius: 50%; background: #0088cc; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; text-decoration: none;" title="Bagikan ke Telegram">
                   ✈️
                </a>
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/donasi/' . $campaign->id)) }}" target="_blank"
                   style="width: 40px; height: 40px; border-radius: 50%; background: #1877F2; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; text-decoration: none; font-weight: 800;" title="Bagikan ke Facebook">
                   f
                </a>
                <!-- Instagram -->
                <button onclick="navigator.clipboard.writeText('{{ url('/donasi/' . $campaign->id) }}'); alert('Tautan disalin! Silakan tempel (paste) di Bio atau Story Instagram Anda.')"
                   style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: none; cursor: pointer;" title="Salin untuk Instagram">
                   📸
                </button>
                <!-- TikTok -->
                <button onclick="navigator.clipboard.writeText('{{ url('/donasi/' . $campaign->id) }}'); alert('Tautan disalin! Silakan tempel (paste) di komentar atau Bio TikTok Anda.')"
                   style="width: 40px; height: 40px; border-radius: 50%; background: #000000; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: none; cursor: pointer; text-shadow: -1px -1px 0 #00f2fe, 1px 1px 0 #fe0979;" title="Salin untuk TikTok">
                   🎵
                </button>
            </div>
        </div>

        {{-- Lapor Mitra --}}
        @auth
            @if($mitra && auth()->id() !== $mitra->user_id)
            <div style="margin-top: 30px; text-align: center; border-top: 1px solid var(--border); padding-top: 20px;">
                <button onclick="document.getElementById('laporModal').style.display='flex'" style="background: none; border: none; color: #dc2626; font-size: 0.85rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    🚩 Laporkan Mitra Ini
                </button>
            </div>
            @endif
        @endauth
    </div>
</div>

{{-- Modal Lapor Mitra --}}
@auth
    @if($mitra && auth()->id() !== $mitra->user_id)
    <div id="laporModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: var(--bg-card); width: 100%; max-width: 450px; border-radius: 20px; padding: 30px; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <button type="button" onclick="document.getElementById('laporModal').style.display='none'" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);">✕</button>
            <h3 style="font-family: var(--font-display); font-size: 1.4rem; margin-bottom: 5px; color: #dc2626;">🚩 Laporkan Mitra</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">Laporkan <strong>{{ $mitra->nama_instansi }}</strong> jika terindikasi penipuan atau melanggar aturan.</p>

            <form method="POST" action="{{ url('/donasi/laporkan-mitra') }}">
                @csrf
                <input type="hidden" name="mitra_id" value="{{ $mitra->user_id }}">
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Alasan Pelaporan</label>
                    <select name="alasan" required style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; font-size: 0.9rem; background: var(--bg-surface); appearance: auto;">
                        <option value="Penipuan / Fiktif">Penipuan / Kampanye Fiktif</option>
                        <option value="Penyalahgunaan Buku">Penyalahgunaan Buku Donasi</option>
                        <option value="Data Instansi Palsu">Data Instansi Palsu</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Detail Tambahan</label>
                    <textarea name="detail_tambahan" rows="4" placeholder="Ceritakan kronologi atau bukti yang Anda miliki..." required style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 10px; font-size: 0.9rem; background: var(--bg-surface); resize: vertical;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; border-radius: 50px; font-weight: 700; font-size: 1rem; background: #dc2626; border: none; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);">Kirim Laporan</button>
            </form>
        </div>
    </div>
    @endif
@endauth

@endsection
