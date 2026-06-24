@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'communities';
    use App\Helpers\BookVerseHelper;
@endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">🏘️ Kelola Komunitas</h2>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <h3 style="margin-top: 0; margin-bottom: 16px; font-size: 1.1rem; color: #1e293b;">📈 Pertumbuhan Global (6 Bulan Terakhir)</h3>
        <canvas id="globalCommunityChart" height="80"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-body" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 8px;text-align:left;">Nama</th>
                    <th style="padding:12px 8px;text-align:left;">Pembuat</th>
                    <th style="padding:12px 8px;text-align:center;">Anggota</th>
                    <th style="padding:12px 8px;text-align:center;">Status</th>
                    <th style="padding:12px 8px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($communities as $c)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:8px;font-weight:600;">{{ $c->nama_komunitas }}</td>
                    <td style="padding:8px;">{{ $c->creator->username ?? 'Unknown' }}</td>
                    <td style="padding:8px;text-align:center;">{{ $c->member_count ?? 0 }}</td>
                    <td style="padding:8px;text-align:center;">{!! BookVerseHelper::statusBadge($c->status) !!}</td>
                    <td style="padding:8px;text-align:center;">
                        <div class="d-flex gap-sm justify-center flex-wrap">
                            <a href="{{ url('/admin/superadmin/communities/detail/' . $c->id) }}" class="btn btn-outline btn-sm">👁️ Detail</a>
                            @if($c->status === 'pending')
                                <form method="POST" action="{{ url('/admin/superadmin/communities/approve') }}" class="inline-form">
                                    @csrf
                                    <input type="hidden" name="community_id" value="{{ $c->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm">✅ Setujui</button>
                                </form>
                                <form method="POST" action="{{ url('/admin/superadmin/communities/reject') }}" class="inline-form">
                                    @csrf
                                    <input type="hidden" name="community_id" value="{{ $c->id }}">
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">❌ Tolak</button>
                                </form>
                            @else
                                <form method="POST" action="{{ url('/admin/superadmin/communities/suspend') }}" class="inline-form">
                                    @csrf
                                    <input type="hidden" name="community_id" value="{{ $c->id }}">
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">
                                        {{ $c->status === 'aktif' ? '⏸️ Nonaktifkan' : '▶️ Aktifkan' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('globalCommunityChart').getContext('2d');
@php
        $defaultChartData = ['labels' => [], 'communities' => [], 'members' => [], 'posts' => []];
    @endphp
    const chartData = {!! json_encode($chartData ?? $defaultChartData) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Komunitas Baru',
                    data: chartData.communities,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Anggota Baru',
                    data: chartData.members,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Postingan Baru',
                    data: chartData.posts,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endsection
