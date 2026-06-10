@extends('layouts.admin')
@section('content')
@php
    $currentAdminPage = 'communities';
    use App\Helpers\BookVerseHelper;
@endphp

<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">🏘️ Kelola Komunitas</h2>

<div class="card">
    <div class="card-body" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);">
                    <th style="padding:12px 8px;text-align:left;">Nama</th>
                    <th style="padding:12px 8px;text-align:left;">Pembuat</th>
                    <th style="padding:12px 8px;text-align:center;">Anggota</th>
                    <th style="padding:12px 8px;text-align:center;">Status</th>
                    <th style="padding:12px 8px;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($communities as $c)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:8px;font-weight:600;">{{ $c->nama_komunitas }}</td>
                    <td style="padding:8px;">{{ $c->creator->username ?? 'Unknown' }}</td>
                    <td style="padding:8px;text-align:center;">{{ $c->member_count ?? 0 }}</td>
                    <td style="padding:8px;text-align:center;">{!! BookVerseHelper::statusBadge($c->status) !!}</td>
                    <td style="padding:8px;text-align:center;">
                        <div class="d-flex gap-sm justify-center flex-wrap">
                            @if($c->status === 'pending')
                                <form method="POST" action="{{ url('/admin/superadmin/communities/approve') }}" class="inline-form">
                                    @csrf
                                    <input type="hidden" name="community_id" value="{{ $c->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm">✅ Setujui</button>
                                </form>
                                <form method="POST" action="{{ url('/admin/superadmin/communities/reject') }}" class="inline-form">
                                    @csrf
                                    <input type="hidden" name="community_id" value="{{ $c->id }}">
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">❌ Tolak</button>
                                </form>
                            @else
                                <form method="POST" action="{{ url('/admin/superadmin/communities/suspend') }}" class="inline-form">
                                    @csrf
                                    <input type="hidden" name="community_id" value="{{ $c->id }}">
                                    <button type="submit" class="btn btn-outline btn-sm">
                                        {{ $c->status === 'aktif' ? '⏸️ Nonaktifkan' : '▶️ Aktifkan' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
