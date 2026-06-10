@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'events'; @endphp

<div class="d-flex align-center justify-between mb-lg">
    <div>
        <a href="{{ url('/admin/komunitas') }}" class="text-muted text-sm mb-sm" style="display:inline-block;">&larr; Kembali ke Dashboard</a>
        <h2 style="font-family:var(--font-display);">📅 Event & Klub Buku</h2>
        <p class="text-muted">Komunitas: {{ $community->nama_komunitas }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-md">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error mb-md">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="d-flex gap-lg align-start">
    <div class="card" style="flex:1;">
        <div class="card-body">
            <h3>Jadwal Mendatang</h3>
            @if($events->count() > 0)
                <div class="d-flex flex-column gap-md mt-md">
                    @foreach($events as $event)
                    <div style="border-left: 4px solid var(--primary); padding-left: 1rem; background: var(--bg-body); padding: 1rem; border-radius: var(--radius-md);">
                        <div class="d-flex justify-between align-start">
                            <div>
                                <h4 style="margin:0 0 0.25rem 0;">{{ $event->judul }}</h4>
                                <p class="text-sm text-muted mb-sm">🕒 {{ $event->tanggal_waktu->format('d M Y, H:i') }} | 📍 {{ $event->lokasi ?: 'Online' }}</p>
                                <p style="margin:0;">{{ $event->deskripsi }}</p>
                            </div>
                            <form action="{{ route('admin.komunitas.events.delete', $event->id) }}" method="POST" onsubmit="return confirm('Batalkan event ini?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline text-danger" style="border-color:var(--danger); color:var(--danger);">Batalkan</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mt-md">Belum ada jadwal event. Ayo buat pertemuan pertamamu!</p>
            @endif
        </div>
    </div>

    <div class="card" style="width:350px;">
        <div class="card-body">
            <h3>Buat Event Baru</h3>
            <form action="{{ route('admin.komunitas.events.create', $community->id) }}" method="POST" class="mt-md">
                @csrf
                <div class="form-group mb-md">
                    <label>Judul Event</label>
                    <input type="text" name="judul" class="form-control" required placeholder="Bahas Buku Hujan by Tere Liye">
                </div>
                <div class="form-group mb-md">
                    <label>Deskripsi Singkat</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Siapkan pertanyaan untuk didiskusikan..."></textarea>
                </div>
                <div class="form-group mb-md">
                    <label>Tanggal & Waktu</label>
                    <input type="datetime-local" name="tanggal_waktu" class="form-control" required>
                </div>
                <div class="form-group mb-md">
                    <label>Lokasi / Link Gmeet</label>
                    <input type="text" name="lokasi" class="form-control" placeholder="https://meet.google.com/xxx">
                </div>
                <button type="submit" class="btn btn-primary w-100">Jadwalkan Event</button>
            </form>
        </div>
    </div>
</div>
@endsection
