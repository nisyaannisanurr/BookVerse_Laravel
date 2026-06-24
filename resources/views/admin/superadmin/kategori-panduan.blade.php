@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'kategori-panduan'; @endphp

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
    max-width: 500px;
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
    <h2 style="font-family:var(--font-display);">📂 Kelola Kategori Panduan</h2>
    <button class="btn btn-primary" onclick="document.getElementById('modalTambah').classList.add('open')">➕ Tambah Kategori</button>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 8px;text-align:left;">ID</th>
                    <th style="padding:12px 8px;text-align:left;">Nama Kategori</th>
                    <th style="padding:12px 8px;text-align:center;width:150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategories as $k)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 8px;font-weight:bold;color:var(--text-muted);">#{{ $k->id }}</td>
                    <td style="padding:12px 8px;"><strong>{{ $k->nama }}</strong></td>
                    <td style="padding:12px 8px;text-align:center;">
                        <button class="btn btn-sm btn-outline" style="padding:4px 8px;font-size:0.8rem;" 
                            onclick="editKategori({{ $k->id }}, '{{ addslashes($k->nama) }}')">
                            ✏️ Edit
                        </button>
                        <form method="POST" action="{{ route('admin.superadmin.kategori-panduan.delete') }}" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                            @csrf
                            <input type="hidden" name="id" value="{{ $k->id }}">
                            <button type="submit" class="btn btn-sm btn-outline" style="padding:4px 8px;font-size:0.8rem;color:var(--danger);border-color:var(--danger);">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding:20px;text-align:center;color:var(--text-muted);">Belum ada data kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal" id="modalTambah">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Tambah Kategori Baru</h3>
            <button type="button" class="close-btn" onclick="document.getElementById('modalTambah').classList.remove('open')">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.superadmin.kategori-panduan.create') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Akun & Keamanan" required>
                </div>
            </div>
            <div class="modal-footer" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal" id="modalEdit">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Kategori</h3>
            <button type="button" class="close-btn" onclick="document.getElementById('modalEdit').classList.remove('open')">✕</button>
        </div>
        <form method="POST" action="" id="editForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama" id="editNama" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary" style="width:100%;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editKategori(id, nama) {
    document.getElementById('editForm').action = '/admin/superadmin/kategori-panduan/' + id + '/edit';
    document.getElementById('editNama').value = nama;
    document.getElementById('modalEdit').classList.add('open');
}
</script>
@endsection
