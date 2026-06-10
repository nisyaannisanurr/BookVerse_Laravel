@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'dashboard'; @endphp

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1>📊 Dashboard Superadmin</h1>
        <p>Selamat datang kembali, {{ auth()->user()->username }}! Ini ringkasan sistem BookVerse.</p>
    </div>
    <div style="font-size:0.78rem;color:#9b90a8;background:white;border:1px solid #e8e0f0;border-radius:10px;padding:8px 14px;">
        📅 {{ now()->translatedFormat('l, d F Y') }}
    </div>
</div>

{{-- Stat Cards Grid --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:32px;">
    <div class="stat-card">
        <div class="stat-icon-wrap si-purple">👥</div>
        <div>
            <div class="stat-value">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Total Pengguna</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-blue">📖</div>
        <div>
            <div class="stat-value">{{ $stats['total_books'] }}</div>
            <div class="stat-label">Total Buku</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-green">🏘️</div>
        <div>
            <div class="stat-value">{{ $stats['total_communities'] }}</div>
            <div class="stat-label">Komunitas Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-amber">⏳</div>
        <div>
            <div class="stat-value">{{ $stats['pending_communities'] }}</div>
            <div class="stat-label">Menunggu Persetujuan</div>
            @if($stats['pending_communities'] > 0)
            <div class="stat-change warn">⚠ Perlu tindakan</div>
            @endif
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-pink">🏷️</div>
        <div>
            <div class="stat-value">{{ $stats['total_preloved'] }}</div>
            <div class="stat-label">Listing Preloved</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap si-purple">⭐</div>
        <div>
            <div class="stat-value">{{ $stats['total_ratings'] }}</div>
            <div class="stat-label">Total Rating & Ulasan</div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    {{-- Pending Communities --}}
    <div style="background:white;border:1px solid #e8e0f0;border-radius:16px;overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #f0eef8;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-weight:700;font-size:0.95rem;color:#1a1030;">⏳ Komunitas Pending</div>
                <div style="font-size:0.75rem;color:#9b90a8;margin-top:2px;">Menunggu persetujuanmu</div>
            </div>
            <a href="{{ url('/admin/superadmin/communities') }}" style="font-size:0.78rem;color:#4f3cc9;font-weight:600;text-decoration:none;">Lihat Semua →</a>
        </div>
        @php
            $pendingCommunities = \App\Models\Komunitas::where('status','pending')->with('creator')->latest()->limit(4)->get();
        @endphp
        @if($pendingCommunities->isEmpty())
            <div style="padding:32px;text-align:center;color:#9b90a8;font-size:0.85rem;">✅ Tidak ada yang pending</div>
        @else
            @foreach($pendingCommunities as $com)
            <div style="padding:14px 24px;border-bottom:1px solid #f5f3ff;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:0.85rem;font-weight:600;color:#1a1030;">{{ $com->nama_komunitas }}</div>
                    <div style="font-size:0.72rem;color:#9b90a8;">oleh {{ $com->creator->username ?? '-' }}</div>
                </div>
                <form method="POST" action="{{ url('/admin/superadmin/communities/'.$com->id.'/approve') }}">
                    @csrf
                    <button type="submit" style="background:#4f3cc9;color:white;border:none;border-radius:7px;padding:5px 12px;font-size:0.72rem;font-weight:600;cursor:pointer;">✓ Setujui</button>
                </form>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Quick Nav Links --}}
    <div style="background:white;border:1px solid #e8e0f0;border-radius:16px;overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid #f0eef8;">
            <div style="font-weight:700;font-size:0.95rem;color:#1a1030;">⚡ Aksi Cepat</div>
            <div style="font-size:0.75rem;color:#9b90a8;margin-top:2px;">Pintasan ke halaman pengelolaan</div>
        </div>
        <div style="padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <a href="{{ url('/admin/superadmin/books/create') }}" style="background:#ede9fb;border-radius:12px;padding:16px;text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px;transition:0.2s;" onmouseover="this.style.background='#ddd6fe'" onmouseout="this.style.background='#ede9fb'">
                <span style="font-size:1.5rem;">➕</span>
                <span style="font-size:0.75rem;font-weight:600;color:#4f3cc9;">Tambah Buku</span>
            </a>
            <a href="{{ url('/admin/superadmin/users') }}" style="background:#eff6ff;border-radius:12px;padding:16px;text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px;transition:0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                <span style="font-size:1.5rem;">👥</span>
                <span style="font-size:0.75rem;font-weight:600;color:#1d4ed8;">Kelola User</span>
            </a>
            <a href="{{ url('/admin/superadmin/communities') }}" style="background:#f0fdf4;border-radius:12px;padding:16px;text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px;transition:0.2s;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                <span style="font-size:1.5rem;">🏘️</span>
                <span style="font-size:0.75rem;font-weight:600;color:#16a34a;">Komunitas</span>
            </a>
            <a href="{{ url('/admin/superadmin/preloved') }}" style="background:#fffbeb;border-radius:12px;padding:16px;text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px;transition:0.2s;" onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">
                <span style="font-size:1.5rem;">🏷️</span>
                <span style="font-size:0.75rem;font-weight:600;color:#d97706;">Preloved</span>
            </a>
        </div>
    </div>

</div>

@endsection
