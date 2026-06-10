@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'genres'; @endphp

<div class="page-header">
    <div>
        <h1>🎨 Kelola Genre</h1>
        <p>Tambah, edit, dan hapus genre buku. Genre digunakan di seluruh katalog.</p>
    </div>
    <span style="background:#ede9fb;color:#4f3cc9;padding:6px 14px;border-radius:20px;font-size:0.8rem;font-weight:600;">
        {{ $genres->count() }} genre
    </span>
</div>

<div style="display:grid;grid-template-columns:340px 1fr;gap:20px;align-items:start;">

    {{-- ─── FORM TAMBAH GENRE ──────────────────────────── --}}
    <div style="background:white;border:1px solid #e8e0f0;border-radius:16px;overflow:hidden;position:sticky;top:80px;">
        <div style="padding:18px 22px;border-bottom:1px solid #f0eef8;font-weight:700;font-size:0.92rem;color:#1a1030;">
            ➕ Tambah Genre Baru
        </div>
        <div style="padding:22px;">
            <form method="POST" action="{{ url('/admin/superadmin/genres/create') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Genre *</label>
                    <input type="text" name="nama_genre" class="form-input" placeholder="Contoh: Fiksi Ilmiah" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi (opsional)</label>
                    <textarea name="deskripsi" class="form-textarea" rows="2" placeholder="Penjelasan singkat genre ini..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Warna Badge</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input type="color" name="warna" value="#4f3cc9"
                               style="width:40px;height:38px;border:1px solid #e8e0f0;border-radius:8px;padding:3px;cursor:pointer;">
                        <span style="font-size:0.78rem;color:#9b90a8;">Warna untuk badge genre di halaman buku</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Tambah Genre</button>
            </form>
        </div>
    </div>

    {{-- ─── DAFTAR GENRE ────────────────────────────────── --}}
    <div style="background:white;border:1px solid #e8e0f0;border-radius:16px;overflow:hidden;">
        <div style="padding:18px 22px;border-bottom:1px solid #f0eef8;font-weight:700;font-size:0.92rem;color:#1a1030;">
            📋 Daftar Genre ({{ $genres->count() }})
        </div>

        @if($genres->isEmpty())
        <div style="padding:48px;text-align:center;color:#9b90a8;">
            <div style="font-size:2rem;margin-bottom:8px;">🎨</div>
            <div>Belum ada genre. Tambahkan genre pertama!</div>
        </div>
        @else
        <div>
            @foreach($genres as $genre)
            <div style="padding:16px 22px;border-bottom:1px solid #f5f3ff;display:flex;align-items:flex-start;gap:14px;"
                 id="genre-row-{{ $genre->id }}">

                {{-- Color dot --}}
                <div style="width:12px;height:12px;border-radius:50%;background:{{ $genre->warna ?? '#4f3cc9' }};margin-top:6px;flex-shrink:0;"></div>

                {{-- Info --}}
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:0.9rem;color:#1a1030;">{{ $genre->nama_genre }}</div>
                    @if($genre->deskripsi)
                    <div style="font-size:0.75rem;color:#9b90a8;margin-top:2px;">{{ $genre->deskripsi }}</div>
                    @endif
                    <div style="margin-top:4px;">
                        <span style="background:#f0eef8;color:#5a4f6e;padding:2px 8px;border-radius:10px;font-size:0.68rem;font-weight:600;">
                            {{ $genre->bukus_count }} buku
                        </span>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:6px;flex-shrink:0;">
                    <button onclick="toggleEditForm({{ $genre->id }})"
                            style="background:#eff6ff;color:#1d4ed8;border:none;border-radius:7px;padding:6px 10px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                        ✏️ Edit
                    </button>
                    @if($genre->bukus_count == 0)
                    <form method="POST" action="{{ url('/admin/superadmin/genres/delete') }}"
                          onsubmit="return confirm('Hapus genre \'{{ addslashes($genre->nama_genre) }}\'?')">
                        @csrf
                        <input type="hidden" name="genre_id" value="{{ $genre->id }}">
                        <button type="submit" style="background:#fef2f2;color:#dc2626;border:none;border-radius:7px;padding:6px 10px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                            🗑️
                        </button>
                    </form>
                    @else
                    <div title="Tidak bisa dihapus — masih dipakai {{ $genre->bukus_count }} buku"
                         style="background:#f5f5f5;color:#ccc;border-radius:7px;padding:6px 10px;font-size:0.75rem;cursor:not-allowed;">
                        🗑️
                    </div>
                    @endif
                </div>
            </div>

            {{-- Inline edit form (hidden) --}}
            <div id="edit-form-{{ $genre->id }}" style="display:none;padding:16px 22px;background:#fafafa;border-bottom:1px solid #e8e0f0;">
                <form method="POST" action="{{ url('/admin/superadmin/genres/'.$genre->id.'/edit') }}">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div>
                            <label class="form-label">Nama Genre *</label>
                            <input type="text" name="nama_genre" class="form-input" value="{{ $genre->nama_genre }}" required>
                        </div>
                        <div>
                            <label class="form-label">Warna</label>
                            <input type="color" name="warna" value="{{ $genre->warna ?? '#4f3cc9' }}"
                                   style="width:100%;height:38px;border:1px solid #e8e0f0;border-radius:8px;padding:3px;cursor:pointer;">
                        </div>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-input" value="{{ $genre->deskripsi }}" placeholder="(opsional)">
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary btn-sm">💾 Simpan</button>
                        <button type="button" onclick="toggleEditForm({{ $genre->id }})" class="btn btn-ghost btn-sm">Batal</button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection
