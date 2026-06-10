@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'challenges'; @endphp

<div class="page-header">
    <div>
        <h1>Tantangan Membaca</h1>
        <p>Tantang anggota <b>{{ $community->nama_komunitas }}</b> untuk membaca sejumlah buku dalam periode tertentu.</p>
    </div>
</div>

<div class="stat-card" style="display: block; margin-bottom: 32px;">
    <h3 style="margin-bottom: 16px; font-size: 1.1rem; color: #1a1030;">Buat Tantangan Baru</h3>
    <form action="{{ route('admin.komunitas.challenges.create', $community->id) }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Judul Tantangan</label>
                <input type="text" name="judul" required placeholder="Contoh: Baca 5 Buku Bulan Ini" style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
            </div>
            <div class="form-group">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Target Jumlah Buku</label>
                <input type="number" name="target_buku" required min="1" placeholder="Contoh: 5" style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Deskripsi / Hadiah</label>
            <textarea name="deskripsi" rows="2" placeholder="Jelaskan tantangan ini dan hadiah yang didapatkan..." style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;"></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="form-group">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
            </div>
            <div class="form-group">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="background: #f97316; border-color: #f97316;">Publikasikan Tantangan</button>
    </form>
</div>

<h3 style="margin-bottom: 16px; font-size: 1.2rem; color: #1a1030;">Tantangan Aktif & Riwayat</h3>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    @foreach($challenges as $challenge)
    <div style="background: white; border: 1px solid #e8e0f0; border-radius: 12px; padding: 20px;">
        <h4 style="font-size: 1.1rem; color: #1a1030; margin-bottom: 8px;">{{ $challenge->judul }}</h4>
        <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 16px;">{{ $challenge->deskripsi }}</p>
        
        <div style="display: flex; gap: 16px; margin-bottom: 16px; font-size: 0.85rem; color: #4b5563;">
            <div><strong>Target:</strong> {{ $challenge->target_buku }} Buku</div>
            <div><strong>Peserta:</strong> {{ $challenge->peserta_count }} orang</div>
        </div>
        
        <div style="font-size: 0.8rem; color: #9b90a8; border-top: 1px dashed #e8e0f0; padding-top: 12px;">
            Periode: 
            {{ $challenge->tanggal_mulai ? $challenge->tanggal_mulai->format('d M Y') : 'Kapan saja' }} 
            - 
            {{ $challenge->tanggal_selesai ? $challenge->tanggal_selesai->format('d M Y') : 'Kapan saja' }}
        </div>
    </div>
    @endforeach
    
    @if($challenges->isEmpty())
    <div style="grid-column: 1 / -1; background: white; padding: 32px; border-radius: 12px; text-align: center; color: #9b90a8; border: 1px dashed #e8e0f0;">
        Belum ada tantangan membaca.
    </div>
    @endif
</div>

@endsection
