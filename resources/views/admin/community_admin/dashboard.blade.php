@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'dashboard'; @endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">📊 Dashboard Admin Komunitas</h2>

@if(count($stats) > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card { background: var(--bg-card); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border); text-align: center; }
        .stat-card .val { font-size: 2rem; font-weight: 700; color: var(--primary); }
        .premium-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem; margin-top: 1rem; }
    </style>

    @foreach($stats as $index => $s)
    <div class="card mb-lg" style="border-left: 5px solid {{ $s['community']->tema_warna ?? 'var(--primary)' }};">
        <div class="card-body">
            <div class="d-flex align-center justify-between" style="margin-bottom: 1rem;">
                <h3 style="font-family:var(--font-display); margin: 0;">
                    @if($s['community']->logo_komunitas)
                        <img src="{{ asset('storage/' . $s['community']->logo_komunitas) }}" alt="Logo" style="width: 32px; height: 32px; border-radius: 50%; vertical-align: middle; margin-right: 8px; object-fit: cover;">
                    @endif
                    {{ $s['community']->nama_komunitas }}
                </h3>
                <span class="text-sm px-2 py-1" style="background: rgba(79,70,229,0.1); color: var(--primary); border-radius: var(--radius-full);">ID: {{ $s['community']->id }}</span>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="val">{{ $s['member_count'] }}</div>
                    <div class="text-sm text-muted">Anggota Aktif</div>
                </div>
                <div class="stat-card">
                    <div class="val" style="color: var(--warning);">{{ $s['pending_count'] }}</div>
                    <div class="text-sm text-muted">Menunggu Persetujuan</div>
                </div>
                <div class="stat-card">
                    <div class="val" style="color: var(--success);">{{ $s['post_count'] }}</div>
                    <div class="text-sm text-muted">Total Postingan</div>
                </div>
            </div>

            <div class="card" style="background: var(--bg-body); border: none; padding: 1rem; margin-bottom: 1rem;">
                <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">📈 Tingkat Interaksi (Simulasi)</h4>
                <div style="height: 200px;">
                    <canvas id="chart-{{ $s['community']->id }}"></canvas>
                </div>
            </div>

            <form action="{{ route('admin.komunitas.broadcast', $s['community']->id) }}" method="POST" style="margin-bottom: 1rem; background: var(--bg-body); padding: 1rem; border-radius: var(--radius-md);">
                @csrf
                <label style="font-weight: 600; display: block; margin-bottom: 0.5rem;">📢 Broadcast Pengumuman</label>
                <div class="d-flex gap-sm">
                    <input type="text" name="pesan" class="form-control" placeholder="Tulis pengumuman untuk seluruh anggota..." required style="flex: 1;">
                    <button type="submit" class="btn btn-primary">Kirim Broadcast</button>
                </div>
            </form>

            <div class="premium-actions">
                <a href="{{ url('/admin/komunitas/' . $s['community']->id . '/members') }}" class="btn btn-outline btn-sm">👥 Anggota</a>
                <a href="{{ url('/admin/komunitas/' . $s['community']->id . '/moderation') }}" class="btn btn-outline btn-sm">🛡️ Moderasi Konten</a>
                <a href="{{ url('/admin/komunitas/' . $s['community']->id . '/reports') }}" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: var(--danger);">🚨 Laporan Anggota</a>
                <a href="{{ url('/admin/komunitas/' . $s['community']->id . '/events') }}" class="btn btn-outline btn-sm" style="color: var(--warning); border-color: var(--warning);">📅 Klub Buku & Event</a>
                <a href="{{ url('/admin/komunitas/' . $s['community']->id . '/settings') }}" class="btn btn-outline btn-sm" style="color: var(--success); border-color: var(--success);">🎨 Identitas & Tema</a>
                <a href="{{ url('/community/' . $s['community']->id . '/feed') }}" class="btn btn-primary btn-sm">📝 Kunjungi Feed</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('chart-{{ $s['community']->id }}').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Interaksi (Post/Komentar)',
                        data: [12, 19, 3, 5, 2, {{ $s['post_count'] * 2 + 5 }}],
                        borderColor: '{{ $s['community']->tema_warna ?? '#4f46e5' }}',
                        tension: 0.3,
                        fill: true,
                        backgroundColor: '{{ $s['community']->tema_warna ?? '#4f46e5' }}20'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        });
    </script>
    @endforeach
@else
    @if(isset($pendingCommunities) && count($pendingCommunities) > 0)
        <div class="empty-state" style="background:var(--bg-white); border-color:var(--border);">
            <span class="emoji">⏳</span>
            <h3>Menunggu Persetujuan</h3>
            <p>Anda memiliki {{ count($pendingCommunities) }} komunitas yang sedang ditinjau oleh Superadmin. Mohon tunggu hingga disetujui untuk mulai mengelola.</p>
            <div style="margin-top:var(--space-md); text-align:left; max-width:400px; margin-left:auto; margin-right:auto;">
                @foreach($pendingCommunities as $pc)
                    <div style="padding:10px; border:1px solid var(--border); border-radius:var(--radius-md); margin-bottom:8px;">
                        <strong>{{ $pc->nama_komunitas }}</strong><br>
                        <span class="text-sm text-muted">Status: Pending</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ url('/community/create') }}" class="btn btn-outline mt-md">Ajukan Komunitas Lain</a>
        </div>
    @else
        <div class="empty-state">
            <span class="emoji">🏘️</span>
            <h3>Belum ada komunitas</h3>
            <p>Buat komunitas pertama Anda!</p>
            <a href="{{ url('/community/create') }}" class="btn btn-primary mt-md">Buat Komunitas</a>
        </div>
    @endif
@endif
@endsection
