@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'users';
    use App\Helpers\BookVerseHelper;
@endphp

<div class="d-flex align-center gap-md" style="margin-bottom:var(--space-lg);">
    <a href="{{ route('admin.superadmin.users') }}" class="btn btn-ghost" style="padding:8px;border-radius:50%;">&larr;</a>
    <h2 style="font-family:var(--font-display);margin:0;">✏️ Edit Pengguna</h2>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.superadmin.users.edit', $user->id) }}">
            @csrf
            
            <div style="margin-bottom: var(--space-md); text-align: center;">
                @if($user->foto_profil)
                    <img src="{{ BookVerseHelper::uploadUrl('profiles', $user->foto_profil) }}" alt="Avatar" style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
                    <br>
                    <label style="font-size:0.9rem; color:var(--danger); cursor:pointer;">
                        <input type="checkbox" name="remove_foto" value="1"> Hapus Foto Profil (Reset ke Default)
                    </label>
                @else
                    <div style="width:100px; height:100px; border-radius:50%; background:#e5e7eb; display:flex; align-items:center; justify-content:center; font-size:2.5rem; margin:0 auto 10px auto;">👤</div>
                    <span style="font-size:0.9rem; color:var(--text-muted);">Foto Profil Default</span>
                @endif
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Email (Tidak dapat diubah)</label>
                <input type="email" value="{{ $user->email }}" disabled class="form-control" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:var(--radius-sm);background:#f3f4f6;">
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="form-control" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:var(--radius-sm);">
                @error('username') <span style="color:var(--danger);font-size:0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: var(--space-md);">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Role / Jabatan</label>
                <select name="role_id" class="form-control" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:var(--radius-sm);">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                            {{ $role->nama_role }}
                        </option>
                    @endforeach
                </select>
                <small style="color:var(--text-muted);">Peringatan: Menaikkan jabatan ke Admin Komunitas memberikan hak akses Dasbor Komunitas.</small>
                @error('role_id') <span style="color:var(--danger);font-size:0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: var(--space-lg);">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Bio / Deskripsi</label>
                <textarea name="bio" rows="4" class="form-control" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:var(--radius-sm);">{{ old('bio', $user->bio) }}</textarea>
                @error('bio') <span style="color:var(--danger);font-size:0.85rem;">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex gap-sm">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.superadmin.users') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
