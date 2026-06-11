@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'users';
    use App\Helpers\BookVerseHelper;
@endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">👥 Kelola Pengguna</h2>

<div class="card">
    <div class="card-body" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 8px;text-align:left;">User</th>
                    <th style="padding:12px 8px;text-align:left;">Email</th>
                    <th style="padding:12px 8px;text-align:left;">Role</th>
                    <th style="padding:12px 8px;text-align:left;">Status</th>
                    <th style="padding:12px 8px;text-align:left;">Terdaftar</th>
                    <th style="padding:12px 8px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:8px;">
                        <div class="d-flex align-center gap-sm">
                            @if($u->foto_profil)
                                <img src="{{ BookVerseHelper::uploadUrl('profiles', $u->foto_profil) }}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                            @else
                                <div class="placeholder-img" style="width:32px;height:32px;border-radius:50%;font-size:0.8rem;">👤</div>
                            @endif
                            <span style="font-weight:600;">{{ $u->username }}</span>
                        </div>
                    </td>
                    <td style="padding:8px;">{{ $u->email }}</td>
                    <td style="padding:8px;"><span class="badge badge-info">{{ $u->role->nama_role ?? 'User' }}</span></td>
                    <td style="padding:8px;">
                        @if($u->status_akun === 'suspended')
                            <span class="badge" style="background:#fee2e2;color:#dc2626;">Suspended</span>
                        @else
                            <span class="badge" style="background:#dcfce7;color:#16a34a;">Aktif</span>
                        @endif
                    </td>
                    <td style="padding:8px;">{{ BookVerseHelper::formatTanggal($u->created_at) }}</td>
                    <td style="padding:8px;text-align:center;">
                        <a href="{{ route('admin.superadmin.users.editform', $u->id) }}" class="btn btn-ghost btn-sm" style="color:var(--primary);">✏️</a>
                        
                        <form method="POST" action="{{ route('admin.superadmin.users.suspend') }}" class="inline-form" onsubmit="return confirm('Ubah status suspend user ini?');">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $u->id }}">
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:{{ $u->status_akun === 'suspended' ? 'var(--success)' : 'var(--warning)' }};">
                                {{ $u->status_akun === 'suspended' ? '✅' : '⏸️' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.superadmin.users.delete') }}" class="inline-form" onsubmit="return confirm('PERINGATAN: Menghapus user akan menghapus SEMUA data terkait (komunitas, postingan, preloved). Yakin ingin hapus permanen?')">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $u->id }}">
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
