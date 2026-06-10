@extends('layouts.admin')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<a href="{{ url('/admin/komunitas') }}" class="btn btn-ghost btn-sm mb-md">← Kembali</a>
<h2 style="font-family:var(--font-display);margin-bottom:var(--space-lg);">🛡️ Moderasi — {{ $community->nama_komunitas }}</h2>

<!-- Posts -->
<h3 class="mb-md">📝 Postingan ({{ $posts->count() }})</h3>
@foreach($posts as $post)
<div class="card mb-sm">
    <div class="card-body" style="padding:var(--space-md);">
        <div class="d-flex justify-between align-center">
            <div>
                <span style="font-weight:600;">{{ $post->user->username ?? 'Unknown' }}</span>
                <span class="text-xs text-muted ml-sm">{{ BookVerseHelper::timeAgo($post->created_at) }}</span>
                @if($post->judul)
                    <div class="mt-sm" style="font-weight:600;">{{ $post->judul }}</div>
                @endif
                <p class="text-sm text-secondary mt-sm">{{ BookVerseHelper::truncate($post->konten, 100) }}</p>
            </div>
            <form method="POST" action="{{ url('/admin/komunitas/moderation/delete-post') }}" class="inline-form" onsubmit="return confirm('Hapus postingan ini?')">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">🗑️</button>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Comments -->
<h3 class="mb-md mt-lg">💬 Komentar ({{ $comments->count() }})</h3>
@foreach($comments as $comment)
<div class="card mb-sm">
    <div class="card-body" style="padding:var(--space-md);">
        <div class="d-flex justify-between align-center">
            <div>
                <span style="font-weight:600;">{{ $comment->user->username ?? 'Unknown' }}</span>
                <span class="text-xs text-muted ml-sm">{{ BookVerseHelper::timeAgo($comment->created_at) }}</span>
                <p class="text-sm text-secondary mt-sm">{{ BookVerseHelper::truncate($comment->konten, 100) }}</p>
            </div>
            <form method="POST" action="{{ url('/admin/komunitas/moderation/delete-comment') }}" class="inline-form" onsubmit="return confirm('Hapus komentar ini?')">
                @csrf
                <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);">🗑️</button>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
