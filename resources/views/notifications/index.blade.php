@extends('layouts.main')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<div class="d-flex justify-between align-center mb-lg">
    <h2 style="font-family:var(--font-display);">🔔 Notifikasi</h2>
    @if($notifications->where('is_read', 0)->count() > 0)
    <form method="POST" action="{{ url('/notifications/read-all') }}">
        @csrf
        <button type="submit" class="btn btn-ghost btn-sm">Tandai Semua Dibaca</button>
    </form>
    @endif
</div>

@if($notifications->count() > 0)
    @foreach($notifications as $notif)
        <div class="card mb-sm {{ $notif->is_read ? '' : 'unread' }}" style="{{ $notif->is_read ? '' : 'border-left:3px solid var(--primary);' }}">
            <div class="card-body" style="padding:var(--space-md);">
                <div class="d-flex justify-between align-center">
                    <p class="text-sm">{{ $notif->pesan }}</p>
                    <span class="text-xs text-muted" style="white-space:nowrap;margin-left:var(--space-md);">{{ BookVerseHelper::timeAgo($notif->created_at) }}</span>
                </div>
                @if($notif->url_target && !$notif->is_read)
                    <a href="{{ url($notif->url_target) }}" class="btn btn-ghost btn-sm mt-sm" onclick="markNotifRead({{ $notif->id }})">Lihat →</a>
                @endif
            </div>
        </div>
    @endforeach
@else
    <div class="empty-state">
        <span class="emoji">🔔</span>
        <h3>Belum ada notifikasi</h3>
        <p>Notifikasi akan muncul saat ada aktivitas terkait akunmu.</p>
    </div>
@endif

<script>
function markNotifRead(id) {
    fetch('{{ url("/notifications/read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({id: id})
    });
}
</script>
@endsection
