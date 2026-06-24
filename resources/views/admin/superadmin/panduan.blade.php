@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'panduan'; @endphp

<style>
.modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0; top: 0; 
    width: 100%; height: 100%; 
    background-color: rgba(0,0,0,0.5); 
    align-items: center; justify-content: center;
}
.modal.open {
    display: flex;
}
.modal-box {
    background: white;
    padding: 24px;
    border-radius: 16px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.modal-header h3 { margin: 0; font-family: var(--font-display); }
.close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); }
.close-btn:hover { color: var(--danger); }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; }
.form-control { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; }
.form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-lg);">
    <h2 style="font-family:var(--font-display);">📖 Kelola Panduan (FAQ)</h2>
    <button class="btn btn-primary" onclick="document.getElementById('modalTambah').classList.add('open')">➕ Tambah Panduan</button>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 8px;text-align:left;width:100px;">Urutan</th>
                    <th style="padding:12px 8px;text-align:left;width:150px;">Kategori</th>
                    <th style="padding:12px 8px;text-align:left;">Pertanyaan</th>
                    <th style="padding:12px 8px;text-align:center;width:150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($panduans as $p)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:8px;font-weight:bold;color:var(--text-muted);">#{{ $p->urutan }}</td>
                    <td style="padding:8px;"><span class="badge badge-info">{{ $p->kategori }}</span></td>
                    <td style="padding:8px;max-width:300px;">
                        <strong>{{ $p->pertanyaan }}</strong><br>
                        <small style="color:var(--text-muted);">{{ Str::limit($p->jawaban, 60) }}</small>
                    </td>
                    <td style="padding:8px;text-align:center;">
                        <button class="btn btn-sm btn-outline" style="padding:4px 8px;font-size:0.8rem;" 
                            onclick="editPanduan({{ $p->id }}, '{{ addslashes($p->pertanyaan) }}', '{{ addslashes($p->jawaban) }}', '{{ addslashes($p->kategori) }}', {{ $p->urutan }})">
                            ✏️ Edit
                        </button>
                        <form method="POST" action="{{ route('admin.superadmin.panduan.delete') }}" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus panduan ini?');">
                            @csrf
                            <input type="hidden" name="id" value="{{ $p->id }}">
                            <button type="submit" class="btn btn-sm btn-outline" style="padding:4px 8px;font-size:0.8rem;color:var(--danger);border-color:var(--danger);">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:20px;text-align:center;color:var(--text-muted);">Belum ada data panduan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal" id="modalTambah">
    <div class="modal-box" style="max-width:600px;">
        <div class="modal-header">
            <h3>Tambah Panduan Baru</h3>
            <button type="button" class="close-btn" onclick="document.getElementById('modalTambah').classList.remove('open')">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.superadmin.panduan.create') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->nama }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Pertanyaan</label>
                    <input type="text" name="pertanyaan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Jawaban</label>
                    <textarea name="jawaban" class="form-control" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label>Urutan (Tampil paling kecil di atas)</label>
                    <input type="number" name="urutan" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal" id="modalEdit">
    <div class="modal-box" style="max-width:600px;">
        <div class="modal-header">
            <h3>Edit Panduan</h3>
            <button type="button" class="close-btn" onclick="document.getElementById('modalEdit').classList.remove('open')">✕</button>
        </div>
        <form method="POST" action="" id="editForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" id="editKategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->nama }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Pertanyaan</label>
                    <input type="text" name="pertanyaan" id="editPertanyaan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Jawaban</label>
                    <textarea name="jawaban" id="editJawaban" class="form-control" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="urutan" id="editUrutan" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editPanduan(id, pertanyaan, jawaban, kategori, urutan) {
    document.getElementById('editForm').action = '/admin/superadmin/panduan/' + id + '/edit';
    document.getElementById('editPertanyaan').value = pertanyaan;
    document.getElementById('editJawaban').value = jawaban;
    document.getElementById('editKategori').value = kategori;
    document.getElementById('editUrutan').value = urutan;
    document.getElementById('modalEdit').classList.add('open');
}
</script>
@endsection
