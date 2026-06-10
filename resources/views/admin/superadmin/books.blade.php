@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'books'; use App\Helpers\BookVerseHelper; @endphp

<div class="page-header">
    <div>
        <h1>📖 Kelola Buku</h1>
        <p>Tambah, edit, dan hapus buku dari katalog BookVerse.</p>
    </div>
    <span style="background:#ede9fb;color:#4f3cc9;padding:6px 14px;border-radius:20px;font-size:0.8rem;font-weight:600;">
        {{ $books->count() }} buku
    </span>
</div>

{{-- ─── TAMBAH BUKU ─────────────────────────────────────────── --}}
<div style="background:white;border:1px solid #e8e0f0;border-radius:16px;margin-bottom:24px;overflow:hidden;">
    <div style="padding:20px 24px;border-bottom:1px solid #f0eef8;font-weight:700;font-size:0.95rem;color:#1a1030;">
        ➕ Tambah Buku Baru
    </div>
    <div style="padding:24px;">
        <form method="POST" action="{{ url('/admin/superadmin/books/create') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label class="form-label">Judul Buku *</label>
                    <input type="text" name="judul" class="form-input" placeholder="Contoh: Laskar Pelangi" required>
                </div>
                <div>
                    <label class="form-label">Penulis *</label>
                    <input type="text" name="penulis" class="form-input" placeholder="Nama penulis" required>
                </div>
                <div>
                    <label class="form-label">Genre *</label>
                    <select name="genre_buku" class="form-select" required>
                        <option value="">-- Pilih Genre --</option>
                        @foreach($genres as $g)
                            <option value="{{ $g->nama_genre }}">{{ $g->nama_genre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label class="form-label">Sinopsis</label>
                <textarea name="sinopsis" class="form-textarea" placeholder="Ringkasan cerita buku..." rows="3"></textarea>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="flex:1;">
                    <label class="form-label">Cover Buku (JPG/PNG/WebP, maks 2MB)</label>
                    <input type="file" name="cover_buku" accept="image/jpeg,image/png,image/webp" class="form-input" style="padding:8px;">
                </div>
                <div style="padding-top:20px;">
                    <button type="submit" class="btn btn-primary">➕ Tambah Buku</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ─── DAFTAR BUKU ─────────────────────────────────────────── --}}
<div style="background:white;border:1px solid #e8e0f0;border-radius:16px;overflow:hidden;">
    <div style="padding:20px 24px;border-bottom:1px solid #f0eef8;display:flex;justify-content:space-between;align-items:center;">
        <div style="font-weight:700;font-size:0.95rem;color:#1a1030;">📋 Daftar Buku</div>
        <input type="text" id="bookSearch" placeholder="🔍 Cari judul atau penulis..."
               style="border:1px solid #e8e0f0;border-radius:8px;padding:7px 14px;font-size:0.82rem;width:240px;outline:none;"
               oninput="filterBooks(this.value)">
    </div>
    <div style="overflow-x:auto;">
        <table class="data-table" id="booksTable">
            <thead>
                <tr>
                    <th style="width:60px;">Cover</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Genre</th>
                    <th style="text-align:center;">Rating</th>
                    <th style="text-align:center;width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($books as $book)
                <tr data-search="{{ strtolower($book->judul . ' ' . $book->penulis) }}">
                    <td>
                        @if($book->cover_buku && file_exists(public_path('uploads/covers/'.$book->cover_buku)))
                            <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}" style="width:44px;height:64px;object-fit:cover;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                        @else
                            <div style="width:44px;height:64px;background:linear-gradient(145deg,#ede9fb,#c4b5fd);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">📖</div>
                        @endif
                    </td>
                    <td style="font-weight:600;max-width:200px;">{{ $book->judul }}</td>
                    <td style="color:#5a4f6e;">{{ $book->penulis }}</td>
                    <td>
                        <span style="background:#ede9fb;color:#4f3cc9;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;">
                            {{ $book->genre_buku }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        {!! BookVerseHelper::starRating((float)($book->avg_rating ?? 0), false) !!}
                        <div style="font-size:0.68rem;color:#9b90a8;">{{ $book->total_rating }} ulasan</div>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:6px;justify-content:center;">
                            <a href="{{ url('/admin/superadmin/books/'.$book->id.'/edit') }}"
                               style="background:#eff6ff;color:#1d4ed8;border:none;border-radius:7px;padding:6px 10px;font-size:0.75rem;font-weight:600;text-decoration:none;display:inline-block;">
                                ✏️ Edit
                            </a>
                            <form method="POST" action="{{ url('/admin/superadmin/books/delete') }}" class="inline-form"
                                  onsubmit="return confirm('Hapus buku \'{{ addslashes($book->judul) }}\'?')">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <button type="submit" style="background:#fef2f2;color:#dc2626;border:none;border-radius:7px;padding:6px 10px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($books->isEmpty())
        <div style="padding:48px;text-align:center;color:#9b90a8;">
            <div style="font-size:2.5rem;margin-bottom:12px;">📚</div>
            <div>Belum ada buku. Tambahkan buku pertama!</div>
        </div>
        @endif
    </div>
</div>

<script>
function filterBooks(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('#booksTable tbody tr').forEach(row => {
        row.style.display = (!q || row.dataset.search.includes(q)) ? '' : 'none';
    });
}
</script>
@endsection
