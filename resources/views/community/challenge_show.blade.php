@extends('layouts.main')
@section('content')
@php use App\Helpers\BookVerseHelper; @endphp

<style>
    :root {
        --community-theme: {{ $community->tema_warna ?? 'var(--primary)' }};
    }
    .btn-community { background: var(--community-theme); color: white; border: none; }
    .btn-community:hover { opacity: 0.9; }
    .text-community { color: var(--community-theme); }
</style>

<div class="mb-md">
    <a href="{{ route('community.feed', $community->id) }}" class="btn btn-ghost">← Kembali ke Komunitas</a>
</div>

<!-- Challenge Header -->
<div class="card mb-lg" style="border-radius: 16px; border-top: 5px solid var(--community-theme); box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    <div class="card-body" style="padding: 30px;">
        <h1 style="font-size: 2rem; font-family: var(--font-display); margin-bottom: 10px; color: var(--community-theme);">
            🎯 {{ $challenge->judul }}
        </h1>
        <p class="text-secondary" style="font-size: 1.1rem; line-height: 1.6;">{{ $challenge->deskripsi }}</p>
        
        <div class="d-flex gap-lg mt-md flex-wrap">
            <div style="background: var(--bg-body); padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div class="text-muted text-sm mb-xs">Target</div>
                <div style="font-size: 1.2rem; font-weight: bold; color: var(--text-primary);">📚 {{ $challenge->target_buku }} Buku</div>
            </div>
            <div style="background: var(--bg-body); padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div class="text-muted text-sm mb-xs">Periode</div>
                <div style="font-size: 1.1rem; font-weight: bold; color: var(--text-primary);">📅 {{ BookVerseHelper::formatTanggal($challenge->tanggal_mulai) }} - {{ $challenge->tanggal_selesai ? BookVerseHelper::formatTanggal($challenge->tanggal_selesai) : 'Tanpa Batas' }}</div>
            </div>
            <div style="background: var(--bg-body); padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div class="text-muted text-sm mb-xs">Total Peserta</div>
                <div style="font-size: 1.2rem; font-weight: bold; color: var(--text-primary);">👥 {{ $pesertaQuery->count() }} Orang</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-lg flex-wrap" style="align-items: flex-start;">
    
    <!-- Left Sidebar: Leaderboard -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card" style="border-radius: 12px; position: sticky; top: 100px;">
            <div class="card-header" style="background: var(--bg-body); border-bottom: 1px solid var(--border-color); padding: 15px 20px;">
                <h3 style="margin:0; font-size: 1.2rem; display: flex; align-items: center; gap: 8px;">
                    🏆 Klasemen Peserta
                </h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @if($pesertaQuery->count() > 0)
                    @foreach($pesertaQuery as $index => $peserta)
                        <div style="display: flex; gap: 15px; align-items: center; padding: 15px 20px; border-bottom: 1px solid var(--border-color);">
                            <div style="font-size: 1.4rem; font-weight: bold; width: 25px; text-align: center; color: {{ $index == 0 ? '#fbbf24' : ($index == 1 ? '#9ca3af' : ($index == 2 ? '#b45309' : 'var(--text-muted)')) }};">
                                {{ $index + 1 }}
                            </div>
                            @if($peserta->user->foto_profil)
                                <img src="{{ BookVerseHelper::uploadUrl('profiles', $peserta->user->foto_profil) }}" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 45px; height: 45px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">👤</div>
                            @endif
                            <div style="flex: 1;">
                                <div style="font-weight: bold; font-size: 1.05rem; display: flex; align-items: center; justify-content: space-between;">
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">{{ $peserta->user->username }}</span>
                                    @if($peserta->status === 'completed' || $peserta->buku_dibaca >= $challenge->target_buku)
                                        <span title="Selesai" style="color: #10b981; font-size: 1.2rem;">✓</span>
                                    @endif
                                </div>
                                <div class="text-sm" style="color: var(--text-secondary); margin-top: 4px;">
                                    <span style="font-weight: 600; color: {{ $peserta->buku_dibaca >= $challenge->target_buku ? '#10b981' : 'var(--community-theme)' }};">{{ $peserta->buku_dibaca }}</span> / {{ $challenge->target_buku }} Buku Dibaca
                                </div>
                                <!-- Progress Bar -->
                                <div style="height: 6px; background: var(--border-color); border-radius: 10px; margin-top: 8px; overflow: hidden;">
                                    @php
                                        $percent = ($peserta->buku_dibaca / $challenge->target_buku) * 100;
                                        if($percent > 100) $percent = 100;
                                    @endphp
                                    <div style="height: 100%; width: {{ $percent }}%; background: {{ $percent >= 100 ? '#10b981' : 'var(--community-theme)' }}; border-radius: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="padding: 30px; text-align: center; color: var(--text-muted);">Belum ada peserta yang mengikuti tantangan ini.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Side: Feed -->
    <div style="flex: 2; min-width: 400px; max-width: 800px;">
        <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            📝 Update Perjalanan
        </h3>

        @if($challenge->postingan->count() > 0)
            @foreach($challenge->postingan as $post)
                <div class="post-card fade-in-up" style="border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); margin-bottom: 20px; border: 1px solid var(--border-color); background: var(--bg-card);">
                    <div class="post-header" style="align-items: center; padding: 20px 20px 15px; border-bottom: none;">
                        <div class="d-flex gap-sm align-center">
                            @if($post->user && $post->user->foto_profil)
                                <img src="{{ BookVerseHelper::uploadUrl('profiles', $post->user->foto_profil) }}" alt="" class="post-avatar" style="width:45px; height:45px; object-fit: cover;">
                            @else
                                <div class="post-avatar placeholder-img" style="border-radius:50%;font-size:1.2rem;width:45px;height:45px;">👤</div>
                            @endif
                            <div class="post-meta">
                                <div class="username" style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">
                                    {{ $post->user->username ?? 'Unknown' }}
                                </div>
                                <div class="time" style="font-size: 0.8rem; color: var(--text-muted);">{{ BookVerseHelper::timeAgo($post->created_at) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="padding: 0 20px 15px;">
                        @if($post->judul)
                            <a href="{{ url('/community/post/' . $post->id) }}" class="post-title" style="display:block;color:var(--text-primary);text-decoration:none;font-size:1.2rem;font-weight:700;margin-bottom:10px;">
                                {{ $post->judul }}
                            </a>
                        @endif

                        <div class="post-content" style="font-size: 1rem; line-height: 1.6; color: var(--text-secondary); margin-bottom: 15px;">{!! nl2br(e(BookVerseHelper::truncate($post->konten, 400))) !!}</div>

                        @if($post->gambar)
                            @php
                                $gambars = json_decode($post->gambar, true);
                                if(!is_array($gambars)) $gambars = [$post->gambar];
                                $total = count($gambars);
                            @endphp
                            @if($total > 0)
                            <div style="margin-top: 15px; display: grid; gap: 4px; 
                                grid-template-columns: {{ $total == 1 ? '1fr' : 'repeat(2, 1fr)' }};
                                border-radius: 12px; overflow: hidden; border: {{ $total == 1 ? 'none' : '1px solid var(--border-color)' }};">
                                @foreach(array_slice($gambars, 0, 3) as $index => $g)
                                    <div style="position: relative; cursor: pointer;
                                        @if($total >= 3 && $index == 0) grid-column: span 2; @endif
                                        "
                                        onclick="openLightbox({{ $post->id }}, {{ $index }})">
                                        <img src="{{ BookVerseHelper::uploadUrl('post_images', $g) }}" alt="Gambar" style="width: 100%; height: {{ $total == 1 ? 'auto' : ($total >= 3 && $index == 0 ? '300px' : '200px') }}; max-height: 500px; object-fit: {{ $total == 1 ? 'contain' : 'cover' }}; display: block; {{ $total == 1 ? 'border-radius: 12px; border: 1px solid var(--border-color);' : '' }}">
                                        
                                        @if($total > 3 && $index == 2)
                                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold;">
                                            +{{ $total - 3 }}
                                        </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div id="lightbox-data-{{ $post->id }}" style="display: none;">
                                {!! json_encode(array_map(fn($g) => BookVerseHelper::uploadUrl('post_images', $g), $gambars)) !!}
                            </div>
                            @endif
                        @endif
                    </div>

                    <div class="post-actions" style="padding: 12px 20px; border-top: 1px solid var(--border-color); display:flex; gap:10px;">
                        <a href="{{ url('/community/post/' . $post->id) }}" class="post-action-btn" style="flex:1; justify-content:center; color: var(--text-secondary); font-weight: 600; border-radius: 8px; padding: 8px; transition: background 0.2s ease; text-decoration: none; display: flex; align-items: center; background: rgba(139, 92, 246, 0.05); color: var(--primary);">
                            Lihat Postingan Lengkap →
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state" style="background: white; border-radius: 12px; padding: 60px 20px; border: 1px solid var(--border-color); text-align: center;">
                <span class="emoji" style="font-size: 4rem;">📝</span>
                <h3 style="font-size: 1.4rem; margin-top: 15px;">Belum ada postingan</h3>
                <p style="color: var(--text-secondary);">Buat postingan di komunitas dan tandai tantangan ini untuk membagikan progres Anda!</p>
                <a href="{{ route('community.feed', $community->id) }}" class="btn btn-community mt-md">Buat Postingan</a>
            </div>
        @endif
    </div>
</div>

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

    function openLightbox(postId, startIndex) {
        const dataEl = document.getElementById('lightbox-data-' + postId);
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

@endsection
