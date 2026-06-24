@extends('layouts.admin')

@section('styles')
<style>
    .community-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 20px 40px rgba(67, 56, 202, 0.2);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 30px;
    }
    .community-header::after {
        content: ''; position: absolute; right: -50px; top: -50px;
        width: 300px; height: 300px; background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .c-cover {
        width: 150px; height: 150px; border-radius: 20px; object-fit: cover;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 1; border: 4px solid rgba(255,255,255,0.2);
    }
    .c-info { z-index: 1; flex: 1; }
    .c-badge {
        display: inline-block; padding: 6px 16px; border-radius: 50px;
        font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
        background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); margin-bottom: 12px;
    }
    .c-title { font-size: 2.2rem; font-weight: 800; margin: 0 0 8px; font-family: 'Playfair Display', serif; }
    .c-desc { font-size: 0.95rem; opacity: 0.8; line-height: 1.5; margin-bottom: 16px; max-width: 600px; }
    
    .action-panel {
        background: white; border-radius: 20px; padding: 24px; margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f3f4f6;
        display: flex; justify-content: space-between; align-items: center;
    }
    
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 30px; }
    .stat-item { background: white; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .stat-label { font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 700; margin-bottom: 8px; letter-spacing: 0.05em; }
    .stat-value { font-size: 1.5rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; }
    
    .section-title { font-size: 1.3rem; font-weight: 800; color: #111827; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
    
    .member-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px; }
    .member-card { display: flex; align-items: center; gap: 12px; padding: 12px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; }
    .m-avatar { width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; object-fit: cover; }
    
    .btn-suspend {
        background: #ef4444; color: white; border: none; padding: 12px 24px;
        border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-suspend:hover { background: #dc2626; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2); }
    .btn-restore { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; margin-bottom: 20px; transition: 0.2s; }
    .btn-back:hover { color: #0f172a; }
</style>
@endsection

@section('content')

<a href="{{ url('/admin/superadmin/reports') }}" class="btn-back">← Kembali</a>

<div class="community-header">
    @if($community->banner_komunitas)
        <img src="{{ asset('uploads/banners/' . $community->banner_komunitas) }}" alt="Banner" class="c-cover">
    @else
        @php
            $initials = strtoupper(substr($community->nama_komunitas, 0, 2));
        @endphp
        <div class="c-cover" style="background: rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; font-size:3.5rem; font-weight:800;">
            {{ $initials }}
        </div>
    @endif
    <div class="c-info">
        <div class="c-badge">Komunitas</div>
        <h1 class="c-title">{{ $community->nama_komunitas }}</h1>
        <p class="c-desc">{{ $community->deskripsi }}</p>
        <div style="font-size: 0.85rem; opacity: 0.8;">
            📅 Dibuat pada {{ $community->created_at ? $community->created_at->format('d M Y') : '-' }}
        </div>
    </div>
</div>

<div class="action-panel">
    <div>
        <h3 style="margin: 0 0 4px; font-size: 1.1rem; color: #0f172a;">Status: 
            <span style="color: {{ $community->status == 'aktif' ? '#16a34a' : ($community->status == 'pending' ? '#d97706' : '#dc2626') }};">
                {{ strtoupper($community->status) }}
            </span>
        </h3>
        <p style="margin: 0; font-size: 0.85rem; color: #64748b;">Gunakan tombol di sebelah kanan untuk menangguhkan atau mengaktifkan komunitas ini.</p>
    </div>
    <form method="POST" action="{{ route('admin.superadmin.communities.suspend') }}">
        @csrf
        <input type="hidden" name="komunitas_id" value="{{ $community->id }}">
        @if($community->status == 'aktif')
            <button type="submit" class="btn-suspend" onclick="return confirm('Yakin ingin BANNED/Nonaktifkan komunitas ini?')">
                🚫 Banned Komunitas
            </button>
        @else
            <button type="submit" class="btn-restore" onclick="return confirm('Yakin ingin mengaktifkan kembali komunitas ini?')">
                ✅ Aktifkan Kembali
            </button>
        @endif
    </form>
</div>

<div class="stat-grid">
    <div class="stat-item">
        <div class="stat-label">Admin Utama</div>
        <div class="stat-value" style="font-size: 1.1rem;">
            <img src="{{ $community->creator && $community->creator->foto_profil ? asset('uploads/profiles/' . $community->creator->foto_profil) : 'https://ui-avatars.com/api/?name=Admin' }}" style="width:30px;height:30px;border-radius:50%;" alt="">
            {{ $community->creator->username ?? 'Tidak diketahui' }}
        </div>
    </div>
    <div class="stat-item">
        <div class="stat-label">Total Anggota</div>
        <div class="stat-value">{{ $community->anggota->count() }} Anggota</div>
    </div>
    <div class="stat-item">
        <div class="stat-label">Total Postingan</div>
        <div class="stat-value">{{ $community->postingan->count() }} Post</div>
    </div>
</div>

<h2 class="section-title">👥 Daftar Anggota ({{ $community->anggota->count() }})</h2>
<div class="member-list" style="margin-bottom: 40px;">
    @foreach($community->anggota->take(12) as $member)
        <div class="member-card">
            <img src="{{ $member->user && $member->user->foto_profil ? asset('uploads/profiles/' . $member->user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($member->user->username ?? 'U') }}" class="m-avatar" alt="">
            <div>
                <strong style="display: block; font-size: 0.95rem; color: #1e293b;">{{ $member->user->username ?? 'Unknown' }}</strong>
                <span style="font-size: 0.75rem; color: #64748b;">Bergabung: {{ $member->tanggal_bergabung ? \Carbon\Carbon::parse($member->tanggal_bergabung)->format('d M Y') : '-' }}</span>
            </div>
        </div>
    @endforeach
    @if($community->anggota->count() > 12)
        <div class="member-card" style="justify-content: center; background: #f8fafc;">
            <span style="font-weight: 600; color: #64748b;">+ {{ $community->anggota->count() - 12 }} lainnya</span>
        </div>
    @endif
</div>

<h2 class="section-title">📈 Aktivitas Komunitas (6 Bulan Terakhir)</h2>
<div style="background: white; border-radius: 20px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 40px;">
    <canvas id="communityChart" height="100"></canvas>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('communityChart').getContext('2d');
    @php
        $defaultChartData = ['labels' => [], 'members' => [], 'posts' => []];
    @endphp
    const chartData = {!! json_encode($chartData ?? $defaultChartData) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
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
