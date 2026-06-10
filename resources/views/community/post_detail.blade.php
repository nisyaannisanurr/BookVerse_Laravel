@extends('layouts.main')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<a href="{{ url('/community/' . $post->komunitas_id . '/feed') }}" class="btn btn-ghost btn-sm mb-md">← Kembali ke {{ $community->nama_komunitas }}</a>

<div class="post-card mb-lg fade-in-up">
    <div class="post-header">
        @if($post->user && $post->user->foto_profil)
            <img src="{{ BookVerseHelper::uploadUrl('profiles', $post->user->foto_profil) }}" alt="" class="post-avatar">
        @else
            <div class="post-avatar placeholder-img" style="border-radius:50%;font-size:1rem;">👤</div>
        @endif
        <div class="post-meta">
            <div class="username">
                {{ $post->user->username ?? 'Unknown' }}
                @if($post->user && $post->user->lencana && $post->user->lencana->isNotEmpty())
                    <span style="font-size: 0.9em; margin-left: 4px;">
                        @foreach($post->user->lencana as $ul)
                            <span title="{{ $ul->lencana->nama_lencana }}">{{ $ul->lencana->ikon }}</span>
                        @endforeach
                    </span>
                @endif
            </div>
            <div class="time">{{ $community->nama_komunitas }} · {{ BookVerseHelper::formatTanggalWaktu($post->created_at) }}</div>
        </div>
    </div>

    @if($post->judul)
        <h2 class="post-title">{{ $post->judul }}</h2>
    @endif

    <div class="post-content" style="-webkit-line-clamp:unset; font-size: 1.05rem; line-height: 1.6; margin-bottom: 20px;">{!! nl2br(e($post->konten)) !!}</div>

    @if($post->gambar)
        @php
            $gambars = json_decode($post->gambar, true);
            if(!is_array($gambars)) $gambars = [$post->gambar];
            $total = count($gambars);
        @endphp
        @if($total > 0)
        <div style="margin-bottom: 20px; display: grid; gap: 4px; 
            grid-template-columns: {{ $total == 1 ? '1fr' : 'repeat(2, 1fr)' }};
            border-radius: 12px; overflow: hidden; border: {{ $total == 1 ? 'none' : '1px solid var(--border-color)' }};">
            @foreach(array_slice($gambars, 0, 3) as $index => $g)
                <div style="position: relative; cursor: pointer;
                    @if($total >= 3 && $index == 0) grid-column: span 2; @endif
                    "
                    onclick="openLightboxDetail({{ $index }})">
                    <img src="{{ BookVerseHelper::uploadUrl('post_images', $g) }}" alt="Gambar Postingan" style="width: 100%; height: {{ $total == 1 ? 'auto' : ($total >= 3 && $index == 0 ? '350px' : '200px') }}; max-height: 600px; object-fit: {{ $total == 1 ? 'contain' : 'cover' }}; display: block; {{ $total == 1 ? 'border-radius: 12px; border: 1px solid var(--border-color); margin: 0 auto;' : '' }}">
                    
                    @if($total > 3 && $index == 2)
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: bold;">
                        +{{ $total - 3 }}
                    </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div id="lightbox-data-detail" style="display: none;">
            {!! json_encode(array_map(fn($g) => BookVerseHelper::uploadUrl('post_images', $g), $gambars)) !!}
        </div>
        @endif
    @endif

    <div class="post-actions d-flex justify-between align-center" style="padding-top: 15px; border-top: 1px solid var(--border-color); gap: 15px;">
        @php
            $userLiked = auth()->check() ? $post->likes->contains('user_id', auth()->id()) : false;
        @endphp
        <form method="POST" action="{{ route('community.post.like', $post->id) }}">
            @csrf
            <button type="submit" class="btn btn-sm" style="color: {{ $userLiked ? 'var(--primary)' : 'var(--text-secondary)' }}; font-weight: 600; border-radius: 20px; padding: 6px 15px; transition: background 0.2s ease; background: {{ $userLiked ? 'rgba(139, 92, 246, 0.1)' : '#f3f4f6' }}; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <span style="font-size: 1.1rem;">{{ $userLiked ? '👍' : '🤍' }}</span> {{ $post->likes->count() > 0 ? $post->likes->count() . ' ' : '' }}Suka
            </button>
        </form>

        <div class="d-flex align-center gap-sm">
            @if(auth()->id() !== (int)$post->user_id)
                <form method="POST" action="{{ route('community.report') }}" class="inline-form" onsubmit="const r = prompt('Alasan melapor:'); if(r){ this.alasan.value = r; return true; } return false;">
                    @csrf
                    <input type="hidden" name="komunitas_id" value="{{ $community->id }}">
                    <input type="hidden" name="postingan_id" value="{{ $post->id }}">
                    <input type="hidden" name="alasan" value="">
                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--warning); padding:6px 12px; border-radius:20px; display:flex; align-items:center; gap:4px;"><span style="font-size:1.1rem;">🚩</span> Laporkan ke Admin</button>
                </form>
            @endif

            @php
                $canDelete = (auth()->id() === (int)$post->user_id || (auth()->check() && auth()->user()->isSuperadmin()) || ($community->creator_id == auth()->id()));
                $isCommunityAdmin = (auth()->id() === $community->creator_id);
            @endphp
            @if($isCommunityAdmin)
                <form method="POST" action="{{ route('admin.komunitas.post.pin') }}" class="inline-form">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <button type="submit" class="btn btn-ghost btn-sm" title="{{ $post->is_pinned ? 'Lepas Pin' : 'Pin Postingan' }}" style="padding:6px; border-radius:50%;">
                        {!! $post->is_pinned ? '📍' : '📌' !!}
                    </button>
                </form>
            @endif
            @if($canDelete)
                <form method="POST" action="{{ url('/community/post/delete') }}" class="inline-form" onsubmit="return confirm('Hapus postingan ini?')">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <button type="submit" class="btn btn-ghost btn-sm" title="Hapus" style="color:var(--danger); padding:6px; border-radius:50%;">🗑️</button>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Comment Form -->
<div class="card mb-lg">
    <div class="card-body">
        <h4 class="mb-md">💬 Tulis Komentar</h4>
        <form method="POST" action="{{ url('/community/comment') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="postingan_id" value="{{ $post->id }}">
            <div class="form-group mb-sm">
                <textarea name="konten" class="form-textarea" placeholder="Tulis komentar..." rows="3" style="border-radius: 12px; background: #f9fafb;"></textarea>
            </div>
            <div class="d-flex justify-between align-center">
                <label style="cursor: pointer; display: flex; align-items: center; gap: 6px; color: var(--text-secondary); padding: 6px 12px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                    <span style="font-size: 1.2rem;">📷</span>
                    <span style="font-size: 0.85rem; font-weight: 600;">Upload Foto</span>
                    <input type="file" name="gambar" accept="image/*" style="display: none;" onchange="this.nextElementSibling.textContent = this.files[0] ? this.files[0].name : '';">
                    <span style="font-size: 0.75rem; color: var(--primary); margin-left: 5px;"></span>
                </label>
                <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 8px 24px;">Kirim Komentar</button>
            </div>
        </form>
    </div>
</div>

<!-- Comments -->
<div class="section-title"><span class="emoji">💬</span> Komentar ({{ $comments->count() }})</div>

@if($comments->count() > 0)
<div class="card">
    <div class="card-body">
        @foreach($comments as $comment)
            <div class="comment">
                @if($comment->user && $comment->user->foto_profil)
                    <img src="{{ BookVerseHelper::uploadUrl('profiles', $comment->user->foto_profil) }}" alt="" class="comment-avatar">
                @else
                    <div class="comment-avatar placeholder-img" style="border-radius:50%;font-size:0.8rem;">👤</div>
                @endif
                <div class="comment-body">
                    <span class="username">
                        {{ $comment->user->username ?? 'Unknown' }}
                        @if($comment->user && $comment->user->lencana && $comment->user->lencana->isNotEmpty())
                            <span style="font-size: 0.9em; margin-left: 4px;">
                                @foreach($comment->user->lencana as $ul)
                                    <span title="{{ $ul->lencana->nama_lencana }}">{{ $ul->lencana->ikon }}</span>
                                @endforeach
                            </span>
                        @endif
                    </span>
                    <span class="time">{{ BookVerseHelper::timeAgo($comment->created_at) }}</span>
                    <p class="text" style="font-size: 0.95rem; line-height: 1.5; margin-bottom: 10px;">{!! nl2br(e($comment->konten)) !!}</p>
                    
                    @if($comment->gambar)
                        <div style="margin-bottom: 10px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); display: inline-block;">
                            <img src="{{ BookVerseHelper::uploadUrl('comment_images', $comment->gambar) }}" alt="Gambar Komentar" style="max-width: 100%; max-height: 300px; object-fit: contain; display: block; background: #f9fafb;">
                        </div>
                    @endif

                    <div class="d-flex align-center gap-sm mt-sm">
                        @if(auth()->id() !== (int)$comment->user_id)
                            <form method="POST" action="{{ route('community.report') }}" class="inline-form" onsubmit="const r = prompt('Alasan melapor:'); if(r){ this.alasan.value = r; return true; } return false;">
                                @csrf
                                <input type="hidden" name="komunitas_id" value="{{ $community->id }}">
                                <input type="hidden" name="komentar_id" value="{{ $comment->id }}">
                                <input type="hidden" name="alasan" value="">
                                <button type="submit" class="btn btn-ghost btn-sm" style="font-size:0.75rem;color:var(--warning);padding:2px 8px;" title="Laporkan">🚩 Laporkan</button>
                            </form>
                        @endif

                        @php
                            $canDeleteComment = (auth()->id() === (int)$comment->user_id || (auth()->check() && auth()->user()->isSuperadmin()) || ($community->creator_id == auth()->id()));
                        @endphp
                        @if($canDeleteComment)
                            <form method="POST" action="{{ url('/community/comment/delete') }}" class="inline-form" onsubmit="return confirm('Hapus komentar ini?')">
                                @csrf
                                <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                                <button type="submit" class="btn btn-ghost btn-sm" style="font-size:0.75rem;color:var(--danger);padding:2px 8px;">🗑️ Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
    <div class="empty-state">
        <span class="emoji">💬</span>
        <h3>Belum ada komentar</h3>
        <p>Jadilah yang pertama berkomentar!</p>
    </div>
@endif
@endsection

<!-- Global Lightbox Modal -->
<div id="imageLightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; align-items: center; justify-content: center; flex-direction: column;">
    <div style="position: absolute; top: 20px; right: 30px; color: white; font-size: 3rem; cursor: pointer; font-weight: bold;" onclick="closeLightbox()">&times;</div>
    <div style="position: absolute; top: 50%; left: 30px; color: white; font-size: 3rem; cursor: pointer; user-select: none; transform: translateY(-50%); padding: 20px;" onclick="prevLightboxImage()">&#10094;</div>
    <div style="position: absolute; top: 50%; right: 30px; color: white; font-size: 3rem; cursor: pointer; user-select: none; transform: translateY(-50%); padding: 20px;" onclick="nextLightboxImage()">&#10095;</div>
    
    <img id="lightboxImg" src="" style="max-width: 90%; max-height: 80vh; object-fit: contain;">
    <div id="lightboxCounter" style="color: white; margin-top: 20px; font-size: 1.2rem; font-weight: bold;"></div>
</div>

<script>
    let currentLightboxImages = [];
    let currentLightboxIndex = 0;

    function openLightboxDetail(startIndex) {
        const dataEl = document.getElementById('lightbox-data-detail');
        if (dataEl) {
            currentLightboxImages = JSON.parse(dataEl.textContent);
            currentLightboxIndex = startIndex;
            updateLightbox();
            document.getElementById('imageLightbox').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLightbox() {
        document.getElementById('imageLightbox').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function prevLightboxImage() {
        if (currentLightboxIndex > 0) {
            currentLightboxIndex--;
        } else {
            currentLightboxIndex = currentLightboxImages.length - 1;
        }
        updateLightbox();
    }

    function nextLightboxImage() {
        if (currentLightboxIndex < currentLightboxImages.length - 1) {
            currentLightboxIndex++;
        } else {
            currentLightboxIndex = 0;
        }
        updateLightbox();
    }

    function updateLightbox() {
        document.getElementById('lightboxImg').src = currentLightboxImages[currentLightboxIndex];
        document.getElementById('lightboxCounter').textContent = (currentLightboxIndex + 1) + ' / ' + currentLightboxImages.length;
    }
</script>
