@extends('layouts.admin')

@section('styles')
<style>
    .user-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: 24px; padding: 40px; color: white; margin-bottom: 30px;
        display: flex; align-items: center; gap: 30px; box-shadow: 0 15px 30px rgba(15,23,42,0.15);
    }
    .u-avatar {
        width: 130px; height: 130px; border-radius: 50%; object-fit: cover;
        border: 4px solid rgba(255,255,255,0.2); box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .u-info { flex: 1; }
    .u-name { font-size: 2.2rem; font-weight: 800; margin: 0 0 4px; }
    .u-email { font-size: 1rem; color: #cbd5e1; margin-bottom: 16px; }
    .u-bio { font-size: 0.95rem; line-height: 1.5; background: rgba(0,0,0,0.2); padding: 12px 16px; border-radius: 12px; max-width: 600px; border-left: 4px solid #3b82f6; }
    
    .role-badge { display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; background: #3b82f6; color: white; margin-bottom: 12px; }
    
    .action-panel {
        background: white; border-radius: 20px; padding: 24px; margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f3f4f6;
        display: flex; justify-content: space-between; align-items: center;
    }
    .btn-suspend {
        background: #ef4444; color: white; border: none; padding: 12px 24px;
        border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-suspend:hover { background: #dc2626; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2); }
    .btn-restore { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
    
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 30px; }
    .stat-item { background: white; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; text-align: center; }
    .stat-val { font-size: 2.5rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; font-family: 'Playfair Display', serif; }
    .stat-label { font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    
    .section-title { font-size: 1.3rem; font-weight: 800; color: #111827; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; font-weight: 600; margin-bottom: 20px; transition: 0.2s; }
    .btn-back:hover { color: #0f172a; }
</style>
@endsection

@section('content')

<a href="{{ url('/admin/superadmin/reports') }}" class="btn-back">← Kembali</a>

<div class="user-header">
    <img src="{{ $user->foto_profil ? asset('uploads/profiles/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($user->username) }}" alt="Avatar" class="u-avatar">
    <div class="u-info">
        <div class="role-badge">
            @if($user->role_id == 2) Admin Komunitas
            @elseif($user->role_id == 3) Pengguna Reguler
            @elseif($user->role_id == 4) Mitra Donasi
            @else Admin
            @endif
        </div>
        <h1 class="u-name">{{ $user->username }}</h1>
        <div class="u-email">{{ $user->email }}</div>
        
        @if($user->bio)
            <div class="u-bio">"{{ $user->bio }}"</div>
        @endif
    </div>
</div>

<div class="action-panel">
    <div>
        <h3 style="margin: 0 0 4px; font-size: 1.1rem; color: #0f172a;">Status Akun: 
            <span style="color: {{ $user->status_akun == 'aktif' ? '#16a34a' : '#dc2626' }};">
                {{ strtoupper($user->status_akun) }}
            </span>
        </h3>
        <p style="margin: 0; font-size: 0.85rem; color: #64748b;">Pengguna yang ditangguhkan (Suspended) tidak akan bisa login ke dalam sistem.</p>
    </div>
    <form method="POST" action="{{ route('admin.superadmin.users.suspend') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        @if($user->status_akun == 'aktif')
            <button type="submit" class="btn-suspend" onclick="return confirm('Yakin ingin menangguhkan (Suspend) pengguna ini?')">
                🚫 Suspend Akun
            </button>
        @else
            <button type="submit" class="btn-restore" onclick="return confirm('Yakin ingin memulihkan akun ini?')">
                ✅ Pulihkan Akun
            </button>
        @endif
    </form>
</div>

<div class="stat-grid">
    <div class="stat-item">
        <div class="stat-val">{{ $user->prelovedBooks->count() }}</div>
        <div class="stat-label">Listing Preloved</div>
    </div>
    <div class="stat-item">
        <div class="stat-val">{{ $user->komunitas->count() }}</div>
        <div class="stat-label">Komunitas Diikuti</div>
    </div>
    <div class="stat-item">
        <div class="stat-val">{{ $totalDonasi }}</div>
        <div class="stat-label">Total Buku Didonasikan</div>
    </div>
</div>

@endsection
