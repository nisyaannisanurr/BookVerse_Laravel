@extends('layouts.main')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<h2 class="mb-lg" style="font-family:var(--font-display);">✏️ Edit Profil</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ url('/profile/edit') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group text-center">
                @if($user->foto_profil)
                    <img src="{{ BookVerseHelper::uploadUrl('profiles', $user->foto_profil) }}" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:var(--space-sm);">
                @else
                    <div class="placeholder-img" style="width:80px;height:80px;border-radius:50%;margin:0 auto var(--space-sm);font-size:2rem;">👤</div>
                @endif
                <div class="file-input-wrapper" style="max-width:300px;margin:0 auto;">
                    <input type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp">
                    <div class="file-input-label">📷 Ganti Foto Profil</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-input" required minlength="3" value="{{ $user->username }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" required value="{{ $user->email }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="bio">Bio</label>
                <textarea id="bio" name="bio" class="form-textarea" placeholder="Ceritakan tentang dirimu...">{{ $user->bio }}</textarea>
            </div>

            <div style="padding-top:var(--space-md);border-top:1px solid var(--border);margin-top:var(--space-md);">
                <h4 class="mb-md">🔒 Ganti Password (Opsional)</h4>
                <div class="form-group">
                    <label class="form-label" for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" class="form-input" placeholder="Masukkan password saat ini">
                </div>
                <div class="form-group">
                    <label class="form-label" for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" placeholder="Minimal 8 karakter">
                </div>
            </div>

            <div class="d-flex gap-sm mt-md">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <a href="{{ url('/profile') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
