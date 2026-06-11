@extends('layouts.admin')
@section('title', 'Pengaturan Sistem Global - Superadmin BookVerse')
@section('content')
@php $currentAdminPage = 'settings'; @endphp

<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:30px;">
    <div>
        <h1 style="color:#1a1030; font-size:1.8rem; font-weight:700; font-family:'Playfair Display', serif;">Pengaturan Sistem Global</h1>
        <p style="color:#9b90a8; font-size:0.85rem; margin-top:4px;">Kontrol saklar utama aplikasi. Berhati-hatilah karena perubahan di sini memengaruhi seluruh pengguna.</p>
    </div>
</div>

<div style="max-width:800px;">
    <form method="POST" action="{{ route('admin.superadmin.settings.update') }}">
        @csrf
        
        <!-- Maintenance Mode -->
        <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.03); border:1px solid {{ $maintenance_mode == 'true' ? '#fecdd3' : '#e8e0f0' }}; margin-bottom:20px; transition:0.3s; position:relative; overflow:hidden;">
            @if($maintenance_mode == 'true')
                <div style="position:absolute; top:0; left:0; width:6px; height:100%; background:#e11d48;"></div>
            @endif
            
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 style="font-size:1.1rem; color:#1a1030; font-weight:700; margin-bottom:5px; display:flex; align-items:center; gap:8px;">
                        🚧 Mode Pemeliharaan (Maintenance Mode)
                        @if($maintenance_mode == 'true')
                            <span style="background:#fee2e2; color:#e11d48; font-size:0.7rem; padding:3px 8px; border-radius:20px; font-weight:700;">AKTIF</span>
                        @endif
                    </h3>
                    <p style="font-size:0.85rem; color:#9b90a8; max-width:500px; line-height:1.5;">
                        Jika diaktifkan, semua pengguna biasa (non-Superadmin) tidak akan bisa mengakses website dan akan diarahkan ke halaman "Sedang Diperbaiki". Gunakan ini hanya saat perbaikan mendesak.
                    </p>
                </div>
                
                <label style="position:relative; display:inline-block; width:60px; height:34px;">
                    <input type="checkbox" name="maintenance_mode" value="true" {{ $maintenance_mode == 'true' ? 'checked' : '' }} style="opacity:0; width:0; height:0;">
                    <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:{{ $maintenance_mode == 'true' ? '#e11d48' : '#cbd5e1' }}; transition:.4s; border-radius:34px;">
                        <span style="position:absolute; content:''; height:26px; width:26px; left:4px; bottom:4px; background-color:white; transition:.4s; border-radius:50%; transform:{{ $maintenance_mode == 'true' ? 'translateX(26px)' : 'translateX(0)' }};"></span>
                    </span>
                </label>
            </div>
        </div>

        <!-- Fitur Preloved -->
        <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.03); border:1px solid #e8e0f0; margin-bottom:30px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 style="font-size:1.1rem; color:#1a1030; font-weight:700; margin-bottom:5px; display:flex; align-items:center; gap:8px;">
                        🏷️ Aktifkan Fitur Preloved
                        @if($preloved_enabled == 'false')
                            <span style="background:#f1f5f9; color:#64748b; font-size:0.7rem; padding:3px 8px; border-radius:20px; font-weight:700;">DIMATIKAN</span>
                        @endif
                    </h3>
                    <p style="font-size:0.85rem; color:#9b90a8; max-width:500px; line-height:1.5;">
                        Menghidupkan atau mematikan modul jual beli Preloved di seluruh aplikasi. Jika dimatikan, halaman Preloved tidak bisa diakses oleh siapa pun.
                    </p>
                </div>
                
                <label style="position:relative; display:inline-block; width:60px; height:34px;">
                    <input type="checkbox" name="preloved_enabled" value="true" {{ $preloved_enabled == 'true' ? 'checked' : '' }} style="opacity:0; width:0; height:0;">
                    <span style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:{{ $preloved_enabled == 'true' ? '#10b981' : '#cbd5e1' }}; transition:.4s; border-radius:34px;">
                        <span style="position:absolute; content:''; height:26px; width:26px; left:4px; bottom:4px; background-color:white; transition:.4s; border-radius:50%; transform:{{ $preloved_enabled == 'true' ? 'translateX(26px)' : 'translateX(0)' }};"></span>
                    </span>
                </label>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-primary" style="padding:14px 30px; font-size:1rem; border-radius:12px; box-shadow:0 8px 20px rgba(79,60,201,0.3);">
                💾 Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
