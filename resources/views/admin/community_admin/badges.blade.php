@extends('layouts.admin')
@section('content')
@php $currentAdminPage = 'badges'; @endphp

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1px solid #e8e0f0 !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1a1030 !important;
        padding-left: 0 !important;
    }
</style>
@endsection

<div class="page-header">
    <div>
        <h1>Lencana & Gamifikasi</h1>
        <p>Berikan penghargaan kepada anggota yang aktif di komunitas <b>{{ $community->nama_komunitas }}</b>.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Buat Lencana Baru -->
    <div class="stat-card" style="display: block;">
        <h3 style="margin-bottom: 16px; font-size: 1.1rem; color: #1a1030;">Buat Lencana Baru</h3>
        <form action="{{ route('admin.komunitas.badges.store', $community->id) }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Nama Lencana</label>
                <input type="text" name="nama_lencana" required style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Ikon (Emoji/Teks Pendek)</label>
                <input type="text" name="ikon" placeholder="Contoh: 🏆, 🌟, Ahli" style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Deskripsi</label>
                <textarea name="deskripsi" rows="3" style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Buat Lencana</button>
        </form>
    </div>

    <!-- Berikan Lencana -->
    <div class="stat-card" style="display: block;">
        <h3 style="margin-bottom: 16px; font-size: 1.1rem; color: #1a1030;">Berikan Lencana</h3>
        <form action="{{ route('admin.komunitas.badges.award', $community->id) }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Pilih Anggota</label>
                <select name="user_id" class="select2-anggota" required style="width: 100%;">
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($members as $member)
                        <option value="{{ $member->user->id }}">{{ $member->user->username }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 6px;">Pilih Lencana</label>
                <select name="lencana_id" required style="width: 100%; padding: 10px; border: 1px solid #e8e0f0; border-radius: 8px;">
                    <option value="">-- Pilih Lencana --</option>
                    @foreach($badges as $badge)
                        <option value="{{ $badge->id }}">{{ $badge->ikon }} {{ $badge->nama_lencana }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; background: #eab308; border-color: #eab308;">Berikan Lencana</button>
        </form>
    </div>
</div>

<h3 style="margin: 32px 0 16px; font-size: 1.2rem; color: #1a1030;">Daftar Lencana Komunitas</h3>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
    @foreach($badges as $badge)
    <div style="background: white; border: 1px solid #e8e0f0; border-radius: 12px; padding: 20px; text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 12px;">{{ $badge->ikon }}</div>
        <h4 style="font-size: 1rem; color: #1a1030; margin-bottom: 4px;">{{ $badge->nama_lencana }}</h4>
        <p style="font-size: 0.8rem; color: #9b90a8;">{{ $badge->deskripsi }}</p>
    </div>
    @endforeach
    @if($badges->isEmpty())
    <div style="grid-column: 1 / -1; background: white; padding: 32px; border-radius: 12px; text-align: center; color: #9b90a8; border: 1px dashed #e8e0f0;">
        Belum ada lencana yang dibuat.
    </div>
    @endif
</div>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-anggota').select2({
            placeholder: "-- Pilih Anggota --",
            allowClear: true
        });
    });
</script>
@endsection

@endsection
