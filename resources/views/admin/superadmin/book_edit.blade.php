@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'books'; use App\Helpers\BookVerseHelper; @endphp

<div class="page-header">
    <div>
        <h1>✏️ Edit Buku</h1>
        <p>Perbarui informasi buku "{{ $book->judul }}"</p>
    </div>
    <a href="{{ url('/admin/superadmin/books') }}" class="btn btn-ghost">← Kembali</a>
</div>

<div style="max-width:720px;">
    <div style="background:white;border:1px solid #e8e0f0;border-radius:16px;overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #f0eef8;font-weight:700;font-size:0.95rem;color:#1a1030;">
            📖 Informasi Buku
        </div>
        <div style="padding:28px;">
            <form method="POST" action="{{ url('/admin/superadmin/books/'.$book->id.'/edit') }}" enctype="multipart/form-data">
                @csrf

                {{-- Cover preview + upload --}}
                <div style="display:flex;gap:24px;align-items:flex-start;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid #f0eef8;">
                    <div>
                        @php $hasCover = $book->cover_buku && file_exists(public_path('uploads/covers/'.$book->cover_buku)); @endphp
                        @if($hasCover)
                            <img src="{{ BookVerseHelper::uploadUrl('covers', $book->cover_buku) }}" id="coverPreview"
                                 style="width:100px;height:150px;object-fit:cover;border-radius:10px;box-shadow:0 4px 16px rgba(79,60,201,0.2);">
                        @else
                            <div id="coverPreview" style="width:100px;height:150px;background:linear-gradient(145deg,#ede9fb,#c4b5fd);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:2rem;">📖</div>
                        @endif
                    </div>
                    <div style="flex:1;">
                        <label class="form-label">Ganti Cover Buku</label>
                        <input type="file" name="cover_buku" accept="image/jpeg,image/png,image/webp"
                               class="form-input" style="padding:8px;"
                               onchange="previewCover(this)">
                        <span class="form-hint">JPG/PNG/WebP, maks 2MB. Biarkan kosong jika tidak ingin mengganti.</span>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Judul Buku *</label>
                        <input type="text" name="judul" class="form-input" value="{{ old('judul', $book->judul) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Penulis *</label>
                        <input type="text" name="penulis" class="form-input" value="{{ old('penulis', $book->penulis) }}" required>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Genre *</label>
                    <select name="genre_buku" class="form-select" required>
                        @foreach($genres as $g)
                            <option value="{{ $g->nama_genre }}" {{ $book->genre_buku === $g->nama_genre ? 'selected' : '' }}>
                                {{ $g->nama_genre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:24px;">
                    <label class="form-label">Sinopsis</label>
                    <textarea name="sinopsis" class="form-textarea" rows="6">{{ old('sinopsis', $book->sinopsis) }}</textarea>
                </div>

                <div style="display:flex;gap:12px;">
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <a href="{{ url('/admin/superadmin/books') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewCover(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('coverPreview');
            p.outerHTML = `<img src="${e.target.result}" id="coverPreview" style="width:100px;height:150px;object-fit:cover;border-radius:10px;box-shadow:0 4px 16px rgba(79,60,201,0.2);">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
