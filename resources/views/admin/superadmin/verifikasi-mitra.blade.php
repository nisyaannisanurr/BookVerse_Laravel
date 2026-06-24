@extends('layouts.admin')
@section('styles')
<style>
    .verif-card {
        background: white;
        border: 1px solid #e8e0f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        transition: 0.2s;
    }
    .verif-card:hover { box-shadow: 0 8px 24px rgba(79,60,201,0.08); }
    .verif-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .verif-status {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .verif-pending { background: #FEF3C7; color: #92400e; }
    .verif-approved { background: #DCFCE7; color: #166534; }
    .verif-rejected { background: #FEE2E2; color: #991b1b; }
    .verif-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    .verif-field {
        background: #f8f5ff;
        border-radius: 10px;
        padding: 12px;
    }
    .verif-field-label {
        font-size: 0.7rem;
        color: #9b90a8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }
    .verif-field-value {
        font-size: 0.88rem;
        font-weight: 600;
        color: #1a1030;
    }
    .verif-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        padding-top: 16px;
        border-top: 1px solid #e8e0f0;
    }

    /* Table Styles */
    .mitra-table-card {
        background: white;
        border: 1px solid #e8e0f0;
        border-radius: 16px;
        padding: 24px;
        margin-top: 32px;
    }
    .mitra-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .mitra-table th {
        background: #f8f5ff;
        padding: 12px 16px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        text-align: left;
        border-bottom: 1px solid #e8e0f0;
    }
    .mitra-table td {
        padding: 16px;
        font-size: 0.9rem;
        border-bottom: 1px solid #e8e0f0;
        vertical-align: middle;
    }
    .mitra-table tr:last-child td { border-bottom: none; }
</style>
@endsection

@php $currentAdminPage = 'mitra'; @endphp

@section('content')

<div class="page-header">
    <div>
        <h1>🤝 Kelola Mitra</h1>
        <p>Kelola semua pendaftaran dan data profil mitra donasi buku.</p>
    </div>
</div>

{{-- ─── STATS ─────────────────── --}}
@php
    $pendingCount  = $allMitra->where('status_verifikasi', 'pending')->count();
    $approvedCount = $allMitra->where('status_verifikasi', 'approved')->count();
    $rejectedCount = $allMitra->where('status_verifikasi', 'rejected')->count();
@endphp

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 28px;">
    <div class="stat-card">
        <div class="stat-icon-wrap si-amber">⏳</div>
        <div>
            <div class="stat-value">{{ $pendingCount }}</div>
            <div class="stat-label">Menunggu Verifikasi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-green">✅</div>
        <div>
            <div class="stat-value">{{ $approvedCount }}</div>
            <div class="stat-label">Mitra Terverifikasi</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-red">❌</div>
        <div>
            <div class="stat-value">{{ $rejectedCount }}</div>
            <div class="stat-label">Mitra Ditolak</div>
        </div>
    </div>
</div>

{{-- ─── NOTIFIKASI PENDING ─────────── --}}
@if($pendingVerifications->count() > 0)
    <h2 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; color: #1a1030; margin-bottom: 16px;">
        🔔 Menunggu Verifikasi ({{ $pendingCount }})
    </h2>
    @foreach($pendingVerifications as $v)
        <div class="verif-card" style="border-color: #fcd34d;">
            <div class="verif-header">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #1a1030; margin-bottom: 4px;">
                        🏫 {{ $v->nama_instansi }}
                    </h3>
                    <span style="font-size: 0.82rem; color: #9b90a8;">
                        📍 {{ $v->instansiDaerah->nama_daerah ?? 'Tidak ada desa' }} · Didaftarkan oleh: {{ $v->user->username ?? '?' }} · {{ $v->created_at->format('d M Y H:i') }}
                    </span>
                </div>
                <span class="verif-status verif-pending">⏳ Pending</span>
            </div>

            <div class="verif-actions" style="border: none; padding-top: 0;">
                <a href="{{ url('/admin/superadmin/mitra/' . $v->id) }}" class="btn btn-sm btn-outline" style="text-decoration: none;">
                    👁️ Lihat Detail Profil
                </a>
                <form method="POST" action="{{ url('/admin/superadmin/mitra/approve') }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="verification_id" value="{{ $v->id }}">
                    <button type="submit" class="btn btn-sm" style="background: #16a34a; color: white; padding: 8px 20px; border-radius: 50px; font-weight: 700; border: none; cursor: pointer;"
                            onclick="return confirm('Setujui mitra ini?')">
                        ✅ Setujui
                    </button>
                </form>
                <form method="POST" action="{{ url('/admin/superadmin/mitra/reject') }}" style="display: inline-flex; gap: 8px; align-items: center;">
                    @csrf
                    <input type="hidden" name="verification_id" value="{{ $v->id }}">
                    <input type="text" name="catatan_admin" placeholder="Alasan penolakan (opsional)" style="padding: 8px 12px; border: 1px solid #e8e0f0; border-radius: 8px; font-size: 0.82rem; width: 220px;">
                    <button type="submit" class="btn btn-sm" style="background: #dc2626; color: white; padding: 8px 20px; border-radius: 50px; font-weight: 700; border: none; cursor: pointer;"
                            onclick="return confirm('Tolak mitra ini?')">
                        ❌ Tolak
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@endif

{{-- ─── TABEL SEMUA MITRA ─────────── --}}
<div class="mitra-table-card">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; color: #1a1030; margin-bottom: 20px;">
        📋 Semua Mitra Terdaftar
    </h2>
    <div style="overflow-x: auto;">
        <table class="mitra-table">
            <thead>
                <tr>
                    <th>Instansi</th>
                    <th>Wilayah/Desa</th>
                    <th>Kategori</th>
                    <th>Pendaftar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allMitra as $m)
                    <tr>
                        <td style="font-weight: 600; color:#1a1030;">{{ $m->nama_instansi }}</td>
                        <td>{{ $m->instansiDaerah->nama_daerah ?? '-' }}</td>
                        <td>{{ $m->kategori }}</td>
                        <td>{{ $m->user->username ?? '?' }}</td>
                        <td>
                            <span class="verif-status verif-{{ $m->status_verifikasi }}">
                                {{ ucfirst($m->status_verifikasi) }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ url('/admin/superadmin/mitra/' . $m->id) }}" class="btn btn-sm btn-outline" style="text-decoration: none;">👁️ Profil</a>
                                
                                @if($m->status_verifikasi === 'approved' || $m->status_verifikasi === 'suspended')
                                    <form method="POST" action="{{ url('/admin/superadmin/mitra/suspend') }}">
                                        @csrf
                                        <input type="hidden" name="verification_id" value="{{ $m->id }}">
                                        <button class="btn btn-sm" style="background:#f1f5f9; color:#475569; border:none; padding:6px 12px;">
                                            {{ $m->status_verifikasi === 'suspended' ? '▶️ Aktifkan' : '⏸️ Suspend' }}
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ url('/admin/superadmin/mitra/delete') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini? Akun mereka akan dikembalikan menjadi user biasa.')">
                                    @csrf
                                    <input type="hidden" name="verification_id" value="{{ $m->id }}">
                                    <button class="btn btn-sm" style="background:#fee2e2; color:#dc2626; border:none; padding:6px 12px;">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 30px;">Belum ada data mitra terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>



@endsection
