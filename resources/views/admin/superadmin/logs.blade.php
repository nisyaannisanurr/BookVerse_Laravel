@extends('layouts.admin')

@section('styles')
<style>
    .logs-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border-radius: 20px;
        padding: 32px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(49, 46, 129, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .logs-header-text h1 {
        margin: 0 0 8px;
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
    }
    .logs-header-text p {
        margin: 0;
        color: #c7d2fe;
        font-size: 0.95rem;
    }
    .total-badge {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        padding: 12px 24px;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .logs-card {
        background: white;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }
    
    .logs-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .logs-table th {
        background: #f8fafc;
        padding: 16px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.05em;
        text-align: left;
        border-bottom: 2px solid #e2e8f0;
    }
    .logs-table th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .logs-table th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
    
    .logs-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .logs-table tr:last-child td { border-bottom: none; }
    .logs-table tr:hover td { background: #f8fafc; }
    
    .time-col strong { display: block; color: #0f172a; font-size: 0.95rem; }
    .time-col small { color: #64748b; font-size: 0.8rem; }
    
    .user-info { display: flex; align-items: center; gap: 12px; }
    .user-avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
    .user-name { font-weight: 700; color: #1e293b; font-size: 0.95rem; margin-bottom: 2px; }
    .user-email { color: #64748b; font-size: 0.8rem; }
    
    .action-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    }
    
    .desc-col { color: #334155; font-size: 0.9rem; line-height: 1.5; max-width: 350px; }
    .ip-col { font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: #94a3b8; background: #f8fafc; padding: 4px 8px; border-radius: 6px; }
    
    /* Pagination Fixes */
    nav[role="navigation"] svg { width: 1.25rem; height: 1.25rem; }
    nav[role="navigation"] > div { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
    nav[role="navigation"] span, nav[role="navigation"] a { padding: 8px 12px; font-size: 0.9rem; text-decoration: none; border-radius: 8px; }
</style>
@endsection

@section('content')
<div class="logs-header">
    <div class="logs-header-text">
        <h1>🛡️ Log Aktivitas Sistem</h1>
        <p>Pantau semua jejak digital dan pergerakan pengguna di dalam platform BookVerse secara real-time.</p>
    </div>
    <div class="total-badge">
        <span>📊</span> {{ number_format($logs->total()) }} Catatan
    </div>
</div>

<div class="logs-card">
    <div style="overflow-x:auto;">
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Waktu Kejadian</th>
                    <th>Pengguna</th>
                    <th>Kategori Aksi</th>
                    <th>Detail Aktivitas</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="time-col">
                            <strong>{{ $log->created_at->diffForHumans() }}</strong>
                            <small>{{ $log->created_at->format('d M Y • H:i') }}</small>
                        </td>
                        <td>
                            @if($log->user)
                                <div class="user-info">
                                    <img src="{{ $log->user->foto_profil ? asset('uploads/profiles/' . $log->user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($log->user->username) }}" alt="Avatar" class="user-avatar">
                                    <div>
                                        <div class="user-name">{{ $log->user->username }}</div>
                                        <div class="user-email">{{ $log->user->email }}</div>
                                    </div>
                                </div>
                            @else
                                <div style="color: #ef4444; font-size: 0.85rem; font-weight: 600; display:flex; align-items:center; gap:6px;">
                                    <span>⚠️</span> User Telah Dihapus
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                $aksi = strtolower($log->tipe_aksi);
                                $badgeColor = '#64748b'; $bgBadge = '#f1f5f9'; $icon = '🔹';
                                
                                if (str_contains($aksi, 'login') || str_contains($aksi, 'auth')) {
                                    $badgeColor = '#2563eb'; $bgBadge = '#dbeafe'; $icon = '🔑';
                                } elseif (str_contains($aksi, 'register')) {
                                    $badgeColor = '#16a34a'; $bgBadge = '#dcfce7'; $icon = '🎉';
                                } elseif (str_contains($aksi, 'logout')) {
                                    $badgeColor = '#dc2626'; $bgBadge = '#fee2e2'; $icon = '🚪';
                                } elseif (str_contains($aksi, 'komunitas')) {
                                    $badgeColor = '#7c3aed'; $bgBadge = '#ede9fe'; $icon = '🏘️';
                                } elseif (str_contains($aksi, 'preloved')) {
                                    $badgeColor = '#d97706'; $bgBadge = '#fef3c7'; $icon = '📚';
                                } elseif (str_contains($aksi, 'donasi')) {
                                    $badgeColor = '#059669'; $bgBadge = '#d1fae5'; $icon = '🎁';
                                } elseif (str_contains($aksi, 'report') || str_contains($aksi, 'laporan')) {
                                    $badgeColor = '#e11d48'; $bgBadge = '#ffe4e6'; $icon = '🚨';
                                }
                            @endphp
                            <div class="action-badge" style="background: {{ $bgBadge }}; color: {{ $badgeColor }};">
                                <span>{{ $icon }}</span> {{ strtoupper($log->tipe_aksi) }}
                            </div>
                        </td>
                        <td class="desc-col">
                            {{ $log->deskripsi }}
                        </td>
                        <td>
                            <span class="ip-col">{{ $log->ip_address ?: '127.0.0.1' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 60px; text-align: center; color: #94a3b8;">
                            <div style="font-size: 3rem; margin-bottom: 16px;">📭</div>
                            <strong style="font-size: 1.1rem; display: block; color: #64748b;">Belum Ada Aktivitas</strong>
                            <p style="margin-top: 8px;">Sistem belum merekam jejak digital apapun saat ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div style="margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
