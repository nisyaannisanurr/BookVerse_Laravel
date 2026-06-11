@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'reports';
    use App\Helpers\BookVerseHelper;
@endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">🚩 Pusat Laporan Global</h2>

<div class="card">
    <div class="card-body" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 8px;text-align:left;">Pelapor</th>
                    <th style="padding:12px 8px;text-align:left;">Entitas</th>
                    <th style="padding:12px 8px;text-align:left;">Alasan</th>
                    <th style="padding:12px 8px;text-align:left;">Status</th>
                    <th style="padding:12px 8px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:8px;">{{ $r->pelapor->username ?? 'Unknown' }}</td>
                    <td style="padding:8px;">
                        <span class="badge badge-info">{{ strtoupper($r->tipe_entitas) }}</span><br>
                        <small>ID: {{ $r->entitas_id }}</small>
                    </td>
                    <td style="padding:8px; max-width:250px;">
                        <strong>{{ $r->alasan }}</strong><br>
                        <small class="text-muted">{{ BookVerseHelper::truncate($r->detail_tambahan, 50) }}</small>
                    </td>
                    <td style="padding:8px;">
                        @if($r->status === 'pending')
                            <span class="badge" style="background:#fef08a;color:#854d0e;">Pending</span>
                        @elseif($r->status === 'diproses')
                            <span class="badge" style="background:#bfdbfe;color:#1e40af;">Diproses</span>
                        @elseif($r->status === 'selesai')
                            <span class="badge" style="background:#dcfce7;color:#16a34a;">Selesai</span>
                        @else
                            <span class="badge" style="background:#fee2e2;color:#dc2626;">Ditolak</span>
                        @endif
                    </td>
                    <td style="padding:8px;text-align:center;">
                        <form method="POST" action="{{ route('admin.superadmin.reports.resolve') }}" class="inline-form" style="display:flex; gap:5px; flex-direction:column; align-items:center;">
                            @csrf
                            <input type="hidden" name="report_id" value="{{ $r->id }}">
                            <select name="status" class="form-control" style="font-size:0.8rem; padding:4px;" onchange="this.form.submit()">
                                <option value="pending" {{ $r->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="diproses" {{ $r->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="selesai" {{ $r->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditolak" {{ $r->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:20px;text-align:center;color:var(--text-muted);">Belum ada laporan yang masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
