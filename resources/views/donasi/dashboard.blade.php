@extends('layouts.main')
@section('title', 'Dashboard Mitra — BookVerse')
@section('content')

<style>
    .mitra-dash-header {
        background: var(--gradient-warning);
        border-radius: 24px;
        padding: 32px 40px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .mitra-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    .mitra-stat {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 22px;
        text-align: center;
    }
    .mitra-stat-val {
        font-family: var(--font-display);
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }
    .mitra-stat-lbl {
        font-size: 0.82rem;
        color: var(--text-muted);
    }
    .campaign-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .campaign-table th {
        background: var(--bg-surface);
        padding: 12px 16px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    .campaign-table td {
        padding: 14px 16px;
        font-size: 0.88rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .status-pill {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .status-pending { background: #FEF3C7; color: #92400e; }
    .status-active { background: #DCFCE7; color: #166534; }
    .status-completed { background: #E0E7FF; color: #3730a3; }
    .status-rejected { background: #FEE2E2; color: #991b1b; }
    .verification-banner {
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    @media (max-width: 768px) {
        .mitra-stats { grid-template-columns: 1fr; }
        .mitra-dash-header { padding: 24px 20px; }
    }
</style>

{{-- ─── VERIFICATION STATUS ─────────────── --}}
@if($verification && $verification->isPending())
    <div class="verification-banner fade-in-up" style="background: #FEF3C7; border: 1px solid #FDE68A;">
        <span style="font-size: 2.5rem;">⏳</span>
        <div>
            <h3 style="font-weight: 700; color: #92400e; margin-bottom: 4px;">Menunggu Verifikasi</h3>
            <p style="color: #78716c; font-size: 0.9rem;">Dokumen pendaftaran Anda sedang ditinjau oleh admin. Proses ini membutuhkan 1-3 hari kerja. Anda belum bisa membuat kampanye sampai disetujui.</p>
        </div>
    </div>
@elseif($verification && $verification->status_verifikasi === 'rejected')
    <div class="verification-banner fade-in-up" style="background: #FEE2E2; border: 1px solid #FECACA;">
        <span style="font-size: 2.5rem;">❌</span>
        <div>
            <h3 style="font-weight: 700; color: #991b1b; margin-bottom: 4px;">Pendaftaran Ditolak</h3>
            <p style="color: #78716c; font-size: 0.9rem;">
                {{ $verification->catatan_admin ?? 'Pendaftaran Anda tidak memenuhi syarat. Silakan hubungi admin untuk info lebih lanjut.' }}
            </p>
        </div>
    </div>
@endif

{{-- ─── HEADER ──────────────────────────── --}}
<div class="mitra-dash-header fade-in-up">
    <div>
        <h1 style="font-family: var(--font-display); font-size: 1.8rem; font-weight: 800; color: var(--text); margin-bottom: 4px;">
            Dashboard Mitra
        </h1>
        @if($verification)
            <p style="color: #78716c; font-size: 0.95rem;">🏫 {{ $verification->nama_instansi }} · {{ $verification->kategori }}</p>
        @endif
    </div>
    @if($verification && $verification->isApproved())
        <a href="{{ url('/donasi/campaign/create') }}" class="btn btn-primary" style="padding: 12px 28px; border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 6px 18px rgba(217,119,6,0.3);">
            ➕ Buat Kampanye Baru
        </a>
    @endif
</div>

{{-- ─── STATS ───────────────────────────── --}}
<div class="mitra-stats fade-in-up">
    <div class="mitra-stat">
        <div class="mitra-stat-val" style="color: #d97706;">{{ $campaigns->count() }}</div>
        <div class="mitra-stat-lbl">Total Kampanye</div>
    </div>
    <div class="mitra-stat">
        <div class="mitra-stat-val" style="color: #16a34a;">{{ $totalBukuDiterima }}</div>
        <div class="mitra-stat-lbl">Buku Diterima</div>
    </div>
    <div class="mitra-stat">
        <div class="mitra-stat-val" style="color: #7c3aed;">{{ $totalDonatur }}</div>
        <div class="mitra-stat-lbl">Total Donatur</div>
    </div>
</div>

{{-- ─── CAMPAIGNS TABLE ─────────────────── --}}
<div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; overflow: hidden;" class="fade-in-up">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--border);">
        <h2 style="font-weight: 700; font-size: 1.1rem;">📋 Kampanye Saya</h2>
    </div>

    @if($campaigns->count() > 0)
        <div class="table-responsive">
            <table class="campaign-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Target</th>
                        <th>Terkumpul</th>
                        <th>Batas Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $c)
                        <tr>
                            <td style="font-weight: 600;">{{ Str::limit($c->judul, 35) }}</td>
                            <td>{{ $c->target_buku }} buku</td>
                            <td>
                                <strong style="color: #d97706;">{{ $c->terkumpul }}</strong> / {{ $c->target_buku }}
                            </td>
                            <td>{{ $c->batas_waktu->format('d M Y') }}</td>
                            <td>
                                <span class="status-pill status-{{ $c->status }}">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td>
                                @if($c->status === 'active' || $c->status === 'completed')
                                    <a href="{{ url('/donasi/campaign/' . $c->id . '/donations') }}" style="color: #d97706; font-weight: 600; font-size: 0.82rem; text-decoration: none;">
                                        Lihat Donasi ({{ $c->donasi_buku_count }})
                                    </a>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.82rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="padding: 50px 20px; text-align: center;">
            <span style="font-size: 3rem; display: block; margin-bottom: 12px;">📭</span>
            <p style="color: var(--text-muted);">Belum ada kampanye. Buat kampanye pertama Anda!</p>
        </div>
    @endif
</div>

@endsection
