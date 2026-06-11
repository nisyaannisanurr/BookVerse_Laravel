@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'broadcast';
@endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">📢 Broadcast Notifikasi</h2>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.superadmin.broadcast.send') }}" onsubmit="return confirm('Yakin ingin mengirim notifikasi ini ke SELURUH pengguna?');">
            @csrf
            
            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Tipe Notifikasi</label>
                <select name="tipe" class="form-control" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:var(--radius-sm);">
                    <option value="sistem">Sistem / Pengumuman</option>
                    <option value="peringatan">Peringatan / Keamanan</option>
                    <option value="event">Event / Promo</option>
                </select>
                @error('tipe') <span style="color:var(--danger);font-size:0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Pesan Notifikasi</label>
                <textarea name="pesan" rows="4" required class="form-control" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:var(--radius-sm);" placeholder="Masukkan pesan notifikasi yang akan dikirim ke seluruh pengguna..."></textarea>
                @error('pesan') <span style="color:var(--danger);font-size:0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: var(--space-lg);">
                <label style="display:block;margin-bottom:8px;font-weight:600;">URL Target (Opsional)</label>
                <input type="url" name="url_target" class="form-control" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:var(--radius-sm);" placeholder="Contoh: https://bookverse.com/event">
                <small style="color:var(--text-muted);">Link yang akan dibuka saat user mengklik notifikasi ini.</small>
                @error('url_target') <span style="color:var(--danger);font-size:0.85rem;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-size:1.1rem; font-weight:bold;">Kirim Broadcast 🚀</button>
        </form>
    </div>
</div>
@endsection
