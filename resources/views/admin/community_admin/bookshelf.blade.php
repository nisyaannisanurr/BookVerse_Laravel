@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'bookshelf'; @endphp

<div class="page-header">
    <div>
        <h1>Rak Buku Komunitas</h1>
        <p>Atur daftar buku wajib baca dan rekomendasi untuk anggota <b>{{ $community->nama_komunitas }}</b>.</p>
    </div>
</div>

<div class="stat-card" style="display: block; margin-bottom: 32px;">
    <h3 style="margin-bottom: 16px; font-size: 1.1rem; color: #1a1030;">Tambahkan Buku ke Rak</h3>
    <form action="{{ route('admin.komunitas.bookshelf.add', $community->id) }}" method="POST" style="display: flex; gap: 16px; align-items: flex-end;">
        @csrf
        <div class="form-group" style="flex: 2;">
            <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Pilih Buku</label>
            <select name="buku_id" required style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
                <option value="">-- Pilih Buku --</option>
                @foreach($books as $buku)
                    <option value="{{ $buku->id }}">{{ $buku->judul }} - {{ $buku->penulis }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="flex: 1;">
            <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Tipe</label>
            <select name="tipe" required style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
                <option value="wajib_baca">Wajib Baca (Klub Buku)</option>
                <option value="rekomendasi">Rekomendasi</option>
            </select>
        </div>
        <div class="form-group" style="flex: 2;">
            <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Catatan (Opsional)</label>
            <input type="text" name="catatan_admin" placeholder="Contoh: Baca untuk event minggu depan" style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
        </div>
        <button type="submit" class="btn btn-primary" style="background: #0ea5e9; border-color: #0ea5e9; height: 42px;">Tambahkan</button>
    </form>
</div>

<h3 style="margin-bottom: 16px; font-size: 1.2rem; color: #1a1030;">Daftar Rak Buku Komunitas</h3>
<div style="background: white; border-radius: 16px; border: 1px solid #e8e0f0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f8f9fc; border-bottom: 1px solid #e8e0f0;">
            <tr>
                <th style="padding: 16px 24px; font-size: 0.85rem; color: #6b7280; font-weight: 600;">Buku</th>
                <th style="padding: 16px 24px; font-size: 0.85rem; color: #6b7280; font-weight: 600;">Tipe</th>
                <th style="padding: 16px 24px; font-size: 0.85rem; color: #6b7280; font-weight: 600;">Catatan Admin</th>
                <th style="padding: 16px 24px; font-size: 0.85rem; color: #6b7280; font-weight: 600;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookshelf as $item)
            <tr style="border-bottom: 1px solid #e8e0f0;">
                <td style="padding: 16px 24px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="{{ asset('storage/' . $item->buku->cover_buku) }}" style="width: 40px; height: 56px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div>
                            <div style="font-weight: 600; color: #1a1030; font-size: 0.95rem;">{{ $item->buku->judul }}</div>
                            <div style="font-size: 0.8rem; color: #6b7280;">{{ $item->buku->penulis }}</div>
                        </div>
                    </div>
                </td>
                <td style="padding: 16px 24px;">
                    @if($item->tipe === 'wajib_baca')
                        <span style="background: #fef08a; color: #854d0e; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Wajib Baca</span>
                    @else
                        <span style="background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Rekomendasi</span>
                    @endif
                </td>
                <td style="padding: 16px 24px; color: #4b5563; font-size: 0.9rem;">
                    {{ $item->catatan_admin ?? '-' }}
                </td>
                <td style="padding: 16px 24px;">
                    <form action="{{ route('admin.komunitas.bookshelf.remove', $community->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="buku_id" value="{{ $item->buku_id }}">
                        <button type="submit" class="btn btn-ghost btn-sm" style="color: #ef4444;" onclick="return confirm('Hapus buku ini dari rak komunitas?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($bookshelf->isEmpty())
            <tr>
                <td colspan="4" style="padding: 32px; text-align: center; color: #9b90a8;">Belum ada buku di rak komunitas.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

@endsection
