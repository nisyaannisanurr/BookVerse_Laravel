@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'settings'; @endphp

<div class="d-flex align-center justify-between mb-lg">
    <div>
        <a href="{{ url('/admin/komunitas') }}" class="text-muted text-sm mb-sm" style="display:inline-block;">&larr; Kembali ke Dashboard</a>
        <h2 style="font-family:var(--font-display);">🎨 Kustomisasi Tema Komunitas</h2>
        <p class="text-muted">Komunitas: {{ $community->nama_komunitas }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-md">{{ session('success') }}</div>
@endif

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('admin.komunitas.settings.update', $community->id) }}" method="POST">
            @csrf
            
            <div class="form-group mb-md">
                <label style="font-weight:600;">Warna Tema Utama</label>
                <p class="text-sm text-muted mb-sm">Warna ini akan digunakan untuk mewarnai tombol dan elemen penting di halaman Feed komunitas Anda.</p>
                <div class="d-flex align-center gap-md">
                    <input type="color" name="tema_warna" value="{{ $community->tema_warna ?? '#4f46e5' }}" style="width:50px; height:50px; padding:0; border:none; border-radius:4px; cursor:pointer;">
                    <span class="text-muted">Pilih warna khusus atau gunakan warna default.</span>
                </div>
            </div>

            <div class="form-group mb-md mt-lg" style="opacity:0.6;">
                <label style="font-weight:600;">Logo Komunitas (Segera Hadir)</label>
                <p class="text-sm text-muted mb-sm">Fitur unggah logo masih dalam pengembangan.</p>
                <input type="file" class="form-control" disabled>
            </div>

            <hr style="border:none; border-top:1px solid var(--border); margin:1.5rem 0;">

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection
