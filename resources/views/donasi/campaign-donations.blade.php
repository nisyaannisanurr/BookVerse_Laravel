@extends('layouts.main')
@section('title', 'Donasi Masuk — ' . $campaign->judul)
@section('content')

<style>
    .donations-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .donations-table th {
        background: var(--bg-surface);
        padding: 12px 16px;
        font-size: 0.76rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    .donations-table td {
        padding: 14px 16px;
        font-size: 0.88rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .status-pill {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .resi-form {
        display: inline-flex;
        gap: 6px;
        align-items: center;
    }
    .resi-form input {
        padding: 6px 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.82rem;
        width: 140px;
    }
</style>

{{-- Breadcrumb --}}
<div style="margin-bottom: 16px;" class="fade-in-up">
    <a href="{{ url('/donasi/dashboard') }}" style="color: var(--text-muted); font-size: 0.85rem; text-decoration: none;">← Kembali ke Dashboard</a>
</div>

{{-- Header --}}
<div style="background: var(--gradient-warning); border-radius: 24px; padding: 32px 40px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;" class="fade-in-up">
    <div>
        <h1 style="font-family: var(--font-display); font-size: 1.6rem; font-weight: 800; color: var(--text); margin-bottom: 6px;">
            📦 Donasi Masuk
        </h1>
        <p style="color: #78716c; font-size: 0.95rem;">Kampanye: <strong>{{ $campaign->judul }}</strong></p>
    </div>
    <div style="display: flex; gap: 20px;">
        <div style="text-align: center;">
            <div style="font-size: 1.6rem; font-weight: 800; color: #d97706;">{{ $campaign->terkumpul }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">/ {{ $campaign->target_buku }} buku</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden;" class="fade-in-up">
    @if($donations->count() > 0)
        <div class="table-responsive">
            <table class="donations-table">
                <thead>
                    <tr>
                        <th>Donatur</th>
                        <th>Judul Buku</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                        <th>No. WA</th>
                        <th>Resi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donations as $d)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; flex-shrink: 0;">👤</div>
                                    <span style="font-weight: 600;">{{ $d->user->username ?? 'Anonim' }}</span>
                                </div>
                            </td>
                            <td>{{ Str::limit($d->judul_buku, 30) }}</td>
                            <td>{{ $d->jumlah }}</td>
                            <td>{{ $d->kondisi === 'baru' ? 'Baru' : 'Bekas Layak' }}</td>
                            <td>
                                @php
                                    $waDonatur = $d->no_wa_donatur;
                                    if ($waDonatur && str_starts_with($waDonatur, '0')) {
                                        $waDonatur = '62' . substr($waDonatur, 1);
                                    }
                                @endphp
                                @if($waDonatur)
                                    <span style="font-family: monospace; font-size: 0.82rem;">{{ $d->no_wa_donatur }}</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.82rem;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($d->resi_pengiriman)
                                    <span style="font-family: monospace; font-size: 0.82rem; background: var(--bg-surface); padding: 3px 8px; border-radius: 6px;">{{ $d->resi_pengiriman }}</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.82rem;">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-pill" style="background: {{ $d->status_color }}15; color: {{ $d->status_color }};">
                                    {{ $d->status_label }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                    @if($waDonatur)
                                        <a href="https://wa.me/{{ $waDonatur }}?text={{ urlencode("Halo, ini dari {$campaign->user->mitraVerification->nama_instansi}. Saya menghubungi Anda terkait donasi buku '{$d->judul_buku}' untuk kampanye {$campaign->judul}.") }}" target="_blank" class="btn btn-sm" style="background: #25D366; color: white; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; border: none; text-decoration: none;">
                                            💬 Chat WA
                                        </a>
                                    @endif
                                    
                                    @if($d->status_pengiriman === 'dikirim' || $d->status_pengiriman === 'menunggu_dikirim')
                                        <button type="button" class="btn btn-sm" style="background: #16a34a; color: white; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; border: none; cursor: pointer;"
                                                onclick="openTerimaModal({{ $d->id }}, '{{ addslashes($d->judul_buku) }}')">
                                            ✅ Diterima
                                        </button>
                                    @elseif($d->status_pengiriman === 'diterima')
                                        <span style="color: #16a34a; font-size: 0.82rem; font-weight: 600;">✅ Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="padding: 50px 20px; text-align: center;">
            <span style="font-size: 3rem; display: block; margin-bottom: 12px;">📭</span>
            <p style="color: var(--text-muted);">Belum ada donasi masuk untuk kampanye ini.</p>
        </div>
    @endif
</div>

<!-- Modal Terima Donasi -->
<div id="terimaModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); width: 100%; max-width: 450px; border-radius: 20px; padding: 30px; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <button type="button" onclick="closeTerimaModal()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);">✕</button>
        <h3 style="font-family: var(--font-display); font-size: 1.4rem; margin-bottom: 5px;">Konfirmasi Terima Buku</h3>
        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">Buku: <strong id="modalBookTitle"></strong></p>

        <form method="POST" action="{{ url('/donasi/konfirmasi') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="donasi_id" id="modalDonasiId">
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Foto Bukti Terima (Opsional)</label>
                <input type="file" name="foto_terima" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 10px; font-size: 0.9rem; background: var(--bg-surface);">
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Donatur akan sangat senang melihat foto buku yang sampai.</div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Pesan / Ucapan Terima Kasih (Opsional)</label>
                <textarea name="pesan_terima" rows="3" placeholder="Contoh: Terima kasih banyak atas donasi bukunya! Sangat bermanfaat untuk adik-adik di sini." style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 10px; font-size: 0.9rem; background: var(--bg-surface); resize: vertical;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; border-radius: 50px; font-weight: 700; font-size: 1rem; background: #16a34a; border: none; box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);">✅ Konfirmasi Diterima</button>
        </form>
    </div>
</div>

<script>
    function openTerimaModal(id, title) {
        document.getElementById('modalDonasiId').value = id;
        document.getElementById('modalBookTitle').innerText = title;
        document.getElementById('terimaModal').style.display = 'flex';
    }
    function closeTerimaModal() {
        document.getElementById('terimaModal').style.display = 'none';
    }
</script>

@endsection
