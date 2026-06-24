@extends('layouts.admin')
@section('styles')
<style>
    .daerah-card {
        background: white;
        border: 1px solid #e8e0f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .daerah-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .daerah-table th {
        background: #f8f5ff;
        padding: 12px 16px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        text-align: left;
        border-bottom: 1px solid #e8e0f0;
    }
    .daerah-table td {
        padding: 16px;
        font-size: 0.9rem;
        border-bottom: 1px solid #e8e0f0;
        vertical-align: middle;
    }
    .daerah-table tr:last-child td { border-bottom: none; }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .status-active { background: #DCFCE7; color: #166534; }
    .status-inactive { background: #FEE2E2; color: #991b1b; }
</style>
@endsection

@php $currentAdminPage = 'instansidaerah'; @endphp

@section('content')

<div class="page-header">
    <div>
        <h1>📍 Kelola Wilayah Mitra</h1>
        <p>Atur daftar Instansi Daerah/Desa yang dapat dipilih saat mitra mendaftar.</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('modalAdd').style.display='flex'">
        ➕ Tambah Desa
    </button>
</div>

<div class="daerah-card">
    <div style="overflow-x: auto;">
        <table class="daerah-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Daerah / Desa</th>
                    <th>Status</th>
                    <th>Jumlah Mitra</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daerahs as $d)
                    <tr>
                        <td style="color:#64748b; font-size:0.85rem;">#{{ $d->id }}</td>
                        <td style="font-weight: 600; color:#1a1030;">{{ $d->nama_daerah }}</td>
                        <td>
                            <span class="status-badge {{ $d->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $d->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <span style="background: #f3f4f6; padding: 4px 10px; border-radius: 8px; font-size: 0.82rem; font-weight: 600;">
                                🏫 {{ $d->mitra_verifications_count }} Mitra
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button class="btn btn-sm btn-outline" onclick="openEditModal({{ $d->id }}, '{{ addslashes($d->nama_daerah) }}', {{ $d->is_active ? 'true' : 'false' }})">✏️ Edit</button>
                                @if($d->mitra_verifications_count == 0)
                                    <form method="POST" action="{{ url('/admin/superadmin/instansi-daerah/delete') }}" onsubmit="return confirm('Hapus daerah ini?')">
                                        @csrf
                                        <input type="hidden" name="daerah_id" value="{{ $d->id }}">
                                        <button class="btn btn-sm" style="background:#fee2e2; color:#dc2626; border:none;">🗑️ Hapus</button>
                                    </form>
                                @else
                                    <button class="btn btn-sm" style="background:#f3f4f6; color:#9ca3af; border:none; cursor:not-allowed;" title="Tidak bisa dihapus karena sudah ada mitra">🗑️ Hapus</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b; padding: 30px;">Belum ada data instansi daerah.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Add --}}
<div id="modalAdd" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; width:400px; max-width:90%; padding:24px;">
        <h3 style="margin-bottom:16px; font-size:1.2rem; font-weight:700;">Tambah Desa / Daerah</h3>
        <form method="POST" action="{{ url('/admin/superadmin/instansi-daerah/create') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; margin-bottom:6px; font-size:0.88rem; font-weight:600;">Nama Daerah</label>
                <input type="text" name="nama_daerah" class="form-control" placeholder="Contoh: Bantan Air" required style="width:100%; padding:10px 14px; border:1px solid #e8e0f0; border-radius:8px;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalAdd').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; width:400px; max-width:90%; padding:24px;">
        <h3 style="margin-bottom:16px; font-size:1.2rem; font-weight:700;">Edit Desa / Daerah</h3>
        <form method="POST" id="editForm" action="">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; margin-bottom:6px; font-size:0.88rem; font-weight:600;">Nama Daerah</label>
                <input type="text" name="nama_daerah" id="edit_nama" class="form-control" required style="width:100%; padding:10px 14px; border:1px solid #e8e0f0; border-radius:8px;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_active" id="edit_active" value="1">
                    <span style="font-size:0.88rem; font-weight:600;">Status Aktif (Ditampilkan di form pendaftaran)</span>
                </label>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, nama, isActive) {
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_active').checked = isActive;
        document.getElementById('editForm').action = "{{ url('/admin/superadmin/instansi-daerah') }}/" + id + "/edit";
        document.getElementById('modalEdit').style.display = 'flex';
    }
</script>

@endsection
