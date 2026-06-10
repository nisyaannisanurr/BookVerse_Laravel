@extends('layouts.admin')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<a href="{{ url('/admin/komunitas') }}" class="btn btn-ghost btn-sm mb-md">← Kembali</a>
<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">👥 Anggota — {{ $community->nama_komunitas }}</h2>

<!-- Pending Members -->
@if($pendingMembers->count() > 0)
<div class="card mb-lg">
    <div class="card-body">
        <h3 class="mb-md">⏳ Menunggu Persetujuan ({{ $pendingMembers->count() }})</h3>
        @foreach($pendingMembers as $member)
        <div class="d-flex justify-between align-center" style="padding:var(--space-sm) 0;border-bottom:1px solid var(--border);">
            <div class="d-flex align-center gap-sm">
                @if($member->user && $member->user->foto_profil)
                    <img src="{{ BookVerseHelper::uploadUrl('profiles', $member->user->foto_profil) }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                @else
                    <div class="placeholder-img" style="width:36px;height:36px;border-radius:50%;font-size:0.9rem;">👤</div>
                @endif
                <div>
                    <div style="font-weight:600;">{{ $member->user->username ?? 'Unknown' }}</div>
                    <div class="text-xs text-muted">{{ $member->user->email ?? '' }}</div>
                </div>
            </div>
            <div class="d-flex gap-sm">
                <form method="POST" action="{{ url('/admin/komunitas/members/approve') }}" class="inline-form">
                    @csrf
                    <input type="hidden" name="member_id" value="{{ $member->id }}">
                    <button type="submit" class="btn btn-primary btn-sm">✅ Terima</button>
                </form>
                <form method="POST" action="{{ url('/admin/komunitas/members/reject') }}" class="inline-form">
                    @csrf
                    <input type="hidden" name="member_id" value="{{ $member->id }}">
                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">❌ Tolak</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Approved Members -->
<div class="card">
    <div class="card-body">
        <h3 class="mb-md">✅ Anggota ({{ $approvedMembers->count() }})</h3>
        @foreach($approvedMembers as $member)
        <div class="d-flex align-center gap-sm" style="padding:var(--space-sm) 0;border-bottom:1px solid var(--border);">
            @if($member->user && $member->user->foto_profil)
                <img src="{{ BookVerseHelper::uploadUrl('profiles', $member->user->foto_profil) }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
            @else
                <div class="placeholder-img" style="width:36px;height:36px;border-radius:50%;font-size:0.9rem;">👤</div>
            @endif
            <div>
                <div style="font-weight:600;">{{ $member->user->username ?? 'Unknown' }}</div>
                <div class="text-xs text-muted">{{ $member->user->email ?? '' }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
