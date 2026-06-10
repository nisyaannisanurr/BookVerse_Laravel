@extends('layouts.main')
@section('content')

<h2 class="mb-lg" style="font-family:var(--font-display);">🏷️ {{ $listing ? 'Edit Buku Preloved' : 'Jual Buku Preloved' }}</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $listing ? url('/preloved/' . $listing->id . '/edit') : url('/preloved/create') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" for="judul_buku">Judul Buku</label>
                <input type="text" id="judul_buku" name="judul_buku" class="form-input" required placeholder="Judul buku" value="{{ $listing->judul_buku ?? '' }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="kondisi_buku">Kondisi Buku</label>
                <select id="kondisi_buku" name="kondisi_buku" class="form-select" required>
                    <option value="">Pilih kondisi...</option>
                    <option value="Seperti Baru" {{ ($listing->kondisi_buku ?? '') === 'Seperti Baru' ? 'selected' : '' }}>Seperti Baru</option>
                    <option value="Baik" {{ ($listing->kondisi_buku ?? '') === 'Baik' ? 'selected' : '' }}>Baik</option>
                    <option value="Cukup" {{ ($listing->kondisi_buku ?? '') === 'Cukup' ? 'selected' : '' }}>Cukup</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" class="form-input" required min="0" placeholder="50000" value="{{ $listing->harga ?? '' }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-textarea" required placeholder="Deskripsikan kondisi buku Anda...">{{ $listing->deskripsi ?? '' }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="no_wa">Nomor WhatsApp</label>
                <input type="text" id="no_wa" name="no_wa" class="form-input" required placeholder="08xxxxxxxxxx" value="{{ $listing->no_wa ?? '' }}">
                <span class="form-hint">Nomor WhatsApp yang bisa dihubungi pembeli</span>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Buku</label>
                <div class="file-input-wrapper w-full">
                    <input type="file" name="foto_buku" accept="image/jpeg,image/png,image/webp" {{ $listing ? '' : 'required' }}>
                    <div class="file-input-label">
                        📷 Pilih foto buku (JPG/PNG/WEBP, maks 5MB)
                    </div>
                </div>
            </div>

            <div class="d-flex gap-sm mt-md">
                <button type="submit" class="btn btn-primary">{{ $listing ? '💾 Simpan Perubahan' : '🏷️ Posting Buku' }}</button>
                <a href="{{ url('/preloved') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
