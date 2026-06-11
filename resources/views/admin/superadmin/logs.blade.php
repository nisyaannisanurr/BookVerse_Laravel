@extends('layouts.admin')
@section('title', 'Log Aktivitas Pengguna - Superadmin BookVerse')
@section('content')
@php $currentAdminPage = 'logs'; @endphp

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <div>
        <h1 style="color:#1a1030; font-size:1.8rem; font-weight:700; font-family:'Playfair Display', serif;">Log Aktivitas Pengguna</h1>
        <p style="color:#9b90a8; font-size:0.85rem; margin-top:4px;">Memantau jejak digital pengguna (Login, Komunitas, Preloved, dll) secara realtime.</p>
    </div>
    <div style="background:white; padding:10px 20px; border-radius:12px; font-weight:600; color:#4f3cc9; border:1px solid #e8e0f0; box-shadow:0 4px 12px rgba(79,60,201,0.05);">
        Total: {{ $logs->total() }} Log
    </div>
</div>

<div style="background:white; border-radius:20px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.03); border:1px solid #e8e0f0;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; color:#1a1030;">
            <thead>
                <tr style="border-bottom:2px solid #f0eef8; text-align:left;">
                    <th style="padding:15px; font-weight:600; color:#9b90a8; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">Waktu</th>
                    <th style="padding:15px; font-weight:600; color:#9b90a8; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">Pengguna</th>
                    <th style="padding:15px; font-weight:600; color:#9b90a8; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">Aksi</th>
                    <th style="padding:15px; font-weight:600; color:#9b90a8; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">Deskripsi</th>
                    <th style="padding:15px; font-weight:600; color:#9b90a8; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-bottom:1px solid #f9f8fc; transition:background 0.2s;" onmouseover="this.style.background='#fcfbfe'" onmouseout="this.style.background='transparent'">
                        <td style="padding:15px; font-size:0.9rem; color:#6b7280;">
                            <strong style="color:#1a1030;">{{ $log->created_at->diffForHumans() }}</strong>
                            <br>
                            <small style="opacity:0.8;">{{ $log->created_at->format('d M Y, H:i') }}</small>
                        </td>
                        <td style="padding:15px;">
                            @if($log->user)
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <img src="{{ $log->user->foto_profil ? asset('storage/profiles/' . $log->user->foto_profil) : asset('images/default-avatar.png') }}" 
                                         alt="{{ $log->user->username }}" 
                                         style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid #ede9fb;">
                                    <div>
                                        <div style="font-weight:700; color:#1a1030;">{{ $log->user->username }}</div>
                                        <div style="font-size:0.75rem; color:#9b90a8;">{{ $log->user->email }}</div>
                                    </div>
                                </div>
                            @else
                                <div style="color:#9b90a8; font-style:italic; font-size:0.85rem;">[User Telah Dihapus]</div>
                            @endif
                        </td>
                        <td style="padding:15px;">
                            @php
                                $badgeColor = '#6b7280';
                                $bgBadge = '#f3f4f6';
                                if (str_contains(strtolower($log->tipe_aksi), 'login')) {
                                    $badgeColor = '#2563eb'; $bgBadge = '#dbeafe';
                                } elseif (str_contains(strtolower($log->tipe_aksi), 'register')) {
                                    $badgeColor = '#16a34a'; $bgBadge = '#dcfce7';
                                } elseif (str_contains(strtolower($log->tipe_aksi), 'logout')) {
                                    $badgeColor = '#dc2626'; $bgBadge = '#fee2e2';
                                } elseif (str_contains(strtolower($log->tipe_aksi), 'komunitas')) {
                                    $badgeColor = '#7c3aed'; $bgBadge = '#ede9fe';
                                } elseif (str_contains(strtolower($log->tipe_aksi), 'preloved')) {
                                    $badgeColor = '#d97706'; $bgBadge = '#fef3c7';
                                }
                            @endphp
                            <span style="background:{{ $bgBadge }}; color:{{ $badgeColor }}; padding:6px 14px; border-radius:20px; font-size:0.75rem; font-weight:700;">
                                {{ strtoupper($log->tipe_aksi) }}
                            </span>
                        </td>
                        <td style="padding:15px; font-size:0.9rem; color:#4b5563;">
                            {{ $log->deskripsi }}
                        </td>
                        <td style="padding:15px; font-size:0.8rem; color:#9ca3af; font-family:monospace;">
                            {{ $log->ip_address ?: 'Unknown' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:40px; text-align:center; color:#9b90a8; font-size:0.95rem;">
                            Belum ada aktivitas yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top:24px;">
        {{ $logs->links() }}
    </div>
</div>
@endsection
