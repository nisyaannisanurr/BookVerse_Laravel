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
    .pinned-post { border: 2px solid var(--community-theme) !important; background: linear-gradient(to right, rgba(255,255,255,1), rgba(255,255,255,0.9)), var(--community-theme); }
    html.dark .pinned-post { background: linear-gradient(to right, var(--bg-card), var(--bg-card)), var(--community-theme); }
    
    /* New Layout Styles */
    .widgets-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 30px;
        align-items: stretch;
    }
    .widget-card {
        border-top: 4px solid var(--community-theme);
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border-radius: 12px;
    }
    .widget-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 16px;
    }
    .widget-scroll-content {
        max-height: 220px;
        overflow-y: auto;
        padding-right: 6px;
        flex: 1;
    }
    /* Scrollbar for widgets */
    .widget-scroll-content::-webkit-scrollbar { width: 5px; }
    .widget-scroll-content::-webkit-scrollbar-track { background: transparent; }
    .widget-scroll-content::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 5px; }
    .widget-scroll-content::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

    .fb-feed-container {
        max-width: 850px;
        margin: 0 auto;
    }
</style>

<!-- Community Header -->
<div class="card mb-lg" style="overflow:hidden; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    <div class="placeholder-img" style="height:220px;">
        @if($community->banner_komunitas && file_exists(public_path('uploads/banners/' . $community->banner_komunitas)))
            <img src="{{ BookVerseHelper::uploadUrl('banners', $community->banner_komunitas) }}" alt="" style="width:100%;height:220px;object-fit:cover;">
        @else
            🏘️
        @endif
    </div>
    <div class="card-body" style="position: relative; padding: 25px; padding-top: 60px;">
        @if($community->logo_komunitas)
            <img src="{{ asset('storage/' . $community->logo_komunitas) }}" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; position: absolute; top: -60px; left: 30px; border: 5px solid var(--bg-card); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        @endif
        <div style="margin-left: {{ $community->logo_komunitas ? '140px' : '0' }}; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
            <div>
                <h2 style="font-family:var(--font-display); margin: 0; font-size: 2rem;">{{ $community->nama_komunitas }}</h2>
                <div class="d-flex gap-md mt-sm text-sm text-muted">
                    <span style="font-weight: 500;">👥 {{ $members->count() }} Anggota</span>
                    <span style="font-weight: 500;">📝 {{ $posts->count() }} Postingan</span>
                </div>
                <p class="text-sm text-secondary mt-md mb-0" style="line-height: 1.6; max-width: 600px;">{{ BookVerseHelper::truncate($community->deskripsi, 300) }}</p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('community.chat', $community->id) }}" class="btn" style="background: var(--community-theme); color: white; border-radius: 20px; padding: 10px 24px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 8px; transition: transform 0.2s;">
                    <span style="font-size: 1.2rem;">💬</span> Buka Live Chat
                </a>
                @if(auth()->check() && !auth()->user()->isSuperadmin() && auth()->id() !== $community->creator_id && $members->contains('user_id', auth()->id()))
                <form action="{{ route('community.leave', $community->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari komunitas ini?');" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn" style="background: #fee2e2; color: #dc2626; border-radius: 20px; padding: 10px 24px; font-weight: bold; border: 1px solid #fca5a5; cursor: pointer;">
                        Keluar Komunitas
                    </button>
                </form>
                @endif
                @if(auth()->check() && auth()->id() !== $community->creator_id)
                <button type="button" onclick="openReportModal('komunitas', {{ $community->id }})" class="btn" style="background: transparent; color: #dc2626; border-radius: 20px; padding: 10px 16px; font-weight: bold; border: 1px solid #dc2626; cursor: pointer;">
                    🚩 Laporkan
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Widgets Row (Menu Sejajar di Atas) -->
<div class="widgets-row">
    <!-- Event -->
    <div class="card widget-card">
        <div class="card-body">
            <h3 style="font-size:1.1rem; margin-bottom:12px; display:flex; align-items:center; gap:8px; font-weight:700;" class="text-community">📅 Event Mendatang</h3>
            <div class="widget-scroll-content">
                @if(isset($events) && $events->count() > 0)
                    <div class="d-flex flex-column gap-sm">
                        @foreach($events as $event)
                        <div style="padding: 10px; background: var(--bg-body); border-radius: var(--radius-sm); border-left: 3px solid var(--community-theme);">
                            <h4 style="font-size:0.9rem; margin:0 0 4px 0; line-height: 1.3;">{{ $event->judul }}</h4>
                            <p class="text-xs text-muted mb-sm" style="margin:0 0 4px 0;">{{ $event->tanggal_waktu->format('d M, H:i') }} | {{ $event->lokasi ?: 'Online' }}</p>
                            @if($event->lokasi && str_starts_with($event->lokasi, 'http'))
                                <a href="{{ $event->lokasi }}" target="_blank" class="text-xs text-community" style="text-decoration:underline; font-weight:600;">Gabung Event</a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted mb-0" style="text-align:center; padding: 30px 0;">Belum ada event terjadwal.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Rak Buku -->
    <div class="card widget-card">
        <div class="card-body">
            <h3 style="font-size:1.1rem; margin-bottom:12px; display:flex; align-items:center; gap:8px; font-weight:700;" class="text-community">📚 Rak Buku</h3>
            <div class="widget-scroll-content">
                @if(isset($bookshelf) && $bookshelf->count() > 0)
                    <div class="d-flex flex-column gap-sm">
                        @foreach($bookshelf as $item)
                        <div style="display: flex; gap: 10px; align-items: center; padding: 6px 0; border-bottom: 1px solid var(--border-color);">
                            <img src="{{ BookVerseHelper::uploadUrl('covers', $item->buku->cover_buku) }}" style="width: 35px; height: 50px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <div style="flex: 1; overflow: hidden;">
                                <h4 style="font-size: 0.85rem; margin: 0 0 4px 0; line-height: 1.2; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"><a href="{{ url('/books/' . $item->buku_id) }}" style="color: inherit; text-decoration: none;">{{ $item->buku->judul }}</a></h4>
                                <span style="font-size: 0.65rem; padding: 3px 8px; border-radius: 20px; background: {{ $item->tipe === 'wajib_baca' ? '#fef08a' : '#e0e7ff' }}; color: {{ $item->tipe === 'wajib_baca' ? '#854d0e' : '#3730a3' }}; font-weight: 700;">
                                    {{ $item->tipe === 'wajib_baca' ? 'Wajib Baca' : 'Rekomendasi' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted mb-0" style="text-align:center; padding: 30px 0;">Rak buku masih kosong.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Tantangan Membaca -->
    <div class="card widget-card">
        <div class="card-body">
            <h3 style="font-size:1.1rem; margin-bottom:12px; display:flex; align-items:center; gap:8px; font-weight:700;" class="text-community">🎯 Tantangan</h3>
            <div class="widget-scroll-content">
                @if(isset($challenges) && $challenges->count() > 0)
                    <div class="d-flex flex-column gap-sm">
                        @foreach($challenges as $challenge)
                        <div style="padding: 10px; background: var(--bg-body); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                            <h4 style="font-size: 0.9rem; margin: 0 0 4px 0; line-height:1.2;">
                                <a href="{{ route('community.challenge.show', ['id' => $community->id, 'challengeId' => $challenge->id]) }}" style="color: inherit; text-decoration: none;">{{ $challenge->judul }}</a>
                            </h4>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 8px 0;">Target: {{ $challenge->target_buku }} Buku</p>
                            @if(isset($joinedChallengeIds) && in_array($challenge->id, $joinedChallengeIds))
                                <a href="{{ route('community.challenge.show', ['id' => $community->id, 'challengeId' => $challenge->id]) }}" style="display: block; background: #e0f2fe; color: #0284c7; padding: 6px; border-radius: 6px; font-size: 0.75rem; text-align: center; font-weight: 700; text-decoration: none;">✓ Sedang Mengikuti</a>
                            @else
                                <form action="{{ route('community.challenge.join', $community->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tantangan_id" value="{{ $challenge->id }}">
                                    <button type="submit" class="btn btn-community btn-sm" style="width: 100%; font-size: 0.75rem; padding: 6px; border-radius: 6px;">Ikut Tantangan</button>
                                </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted mb-0" style="text-align:center; padding: 30px 0;">Belum ada tantangan aktif.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Q&A Sesi -->
    <div class="card widget-card">
        <div class="card-body">
            <h3 style="font-size:1.1rem; margin-bottom:12px; display:flex; align-items:center; gap:8px; font-weight:700;" class="text-community">🎙️ Sesi Q&A</h3>
            <div class="widget-scroll-content">
                @if(isset($qnas) && $qnas->count() > 0)
                    <div class="d-flex flex-column gap-sm">
                        @foreach($qnas as $qna)
                        <div style="padding: 12px; background: rgba(139, 92, 246, 0.05); border-radius: var(--radius-sm); border: 1px dashed rgba(139, 92, 246, 0.3);">
                            <h4 style="font-size: 0.85rem; margin: 0 0 4px 0; color: #5b21b6; line-height:1.2;">{{ $qna->judul }}</h4>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 8px 0;">Narasumber: <strong>{{ $qna->narasumber ?? 'Admin' }}</strong></p>
                            <form action="{{ route('community.qna.ask', $community->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="qna_id" value="{{ $qna->id }}">
                                <input type="text" name="pertanyaan" required placeholder="Tulis pertanyaan..." style="width: 100%; padding: 6px 10px; border: 1px solid #e8e0f0; border-radius: 6px; font-size: 0.75rem; margin-bottom: 6px; outline:none;">
                                <button type="submit" class="btn btn-sm" style="background: white; border: 1px solid #8b5cf6; color: #8b5cf6; width: 100%; font-size: 0.75rem; padding: 6px; border-radius: 6px; font-weight:600;">Kirim</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted mb-0" style="text-align:center; padding: 30px 0;">Belum ada sesi Q&A dibuka.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Leaderboard (Anggota Teraktif) -->
    <div class="card widget-card">
        <div class="card-body">
            <h3 style="font-size:1.1rem; margin-bottom:12px; display:flex; align-items:center; gap:8px; font-weight:700;" class="text-community">🏆 Anggota Teraktif</h3>
            <div class="widget-scroll-content">
                @if(isset($topMembers) && $topMembers->count() > 0)
                    <div class="d-flex flex-column gap-sm">
                        @foreach($topMembers as $index => $topMember)
                        <div style="display: flex; gap: 10px; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border-color);">
                            <div style="font-size: 1.2rem; font-weight: bold; color: {{ $index == 0 ? '#fbbf24' : ($index == 1 ? '#9ca3af' : ($index == 2 ? '#b45309' : 'var(--text-muted)')) }}; width: 20px; text-align: center;">
                                {{ $index + 1 }}
                            </div>
                            @if($topMember->foto_profil)
                                <img src="{{ BookVerseHelper::uploadUrl('profiles', $topMember->foto_profil) }}" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">👤</div>
                            @endif
                            <div style="flex: 1; overflow: hidden;">
                                <div style="font-size: 0.85rem; font-weight: 600; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ $topMember->username }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $topMember->post_count }} Postingan</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted mb-0" style="text-align:center; padding: 30px 0;">Belum ada data anggota teraktif.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Center Feed (Kek Facebook) -->
<div class="fb-feed-container">
    
    <!-- New Post Form -->
    <div class="card mb-lg" style="border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid var(--border-color); overflow: hidden;">
        <!-- Top Tabs -->
        <div style="background: #f9fafb; border-bottom: 1px solid var(--border-color); display: flex; padding: 0 10px;">
            <div style="padding: 12px 16px; border-bottom: 3px solid var(--community-theme); color: var(--community-theme); font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <span style="font-size: 1.1rem;">📝</span> Status
            </div>
            <label style="padding: 12px 16px; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                <span style="font-size: 1.1rem;">📷</span> Foto/Video
                <input type="file" name="gambar[]" accept="image/*" form="postForm" style="display: none;" multiple onchange="document.getElementById('fileCount').textContent = this.files.length > 0 ? this.files.length + ' Foto dipilih' : '';">
            </label>
        </div>

        <div class="card-body" style="padding: 15px 20px;">
            <form method="POST" id="postForm" action="{{ url('/community/' . $community->id . '/post') }}" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 15px;">
                    @auth
                        @if(auth()->user()->foto_profil)
                            <img src="{{ BookVerseHelper::uploadUrl('profiles', auth()->user()->foto_profil) }}" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                        @else
                            <div style="width: 45px; height: 45px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">👤</div>
                        @endif
                    @endauth
                    <div style="flex: 1;">
                        <input type="text" name="judul" class="form-input mb-sm" placeholder="Judul diskusi (opsional)" style="background: transparent; border: none; font-size: 1rem; font-weight: 600; padding: 0; outline: none; width: 100%; box-shadow: none;">
                        <textarea name="konten" class="form-textarea" placeholder="Apa yang Anda pikirkan, {{ auth()->check() ? auth()->user()->username : 'Teman' }}?" required rows="2" style="background: transparent; border: none; padding: 0; resize: none; font-size: 1.1rem; outline: none; width: 100%; box-shadow: none;"></textarea>
                        
                        @php
                            $activeChallenges = isset($joinedChallengeIds) && !empty($joinedChallengeIds) 
                                ? collect($challenges)->whereIn('id', $joinedChallengeIds) 
                                : collect();
                        @endphp
                        
                        @if($activeChallenges->count() > 0)
                            <div style="margin-top: 10px;">
                                <select name="tantangan_id" style="padding: 6px 12px; border-radius: 20px; border: 1px solid var(--border-color); font-size: 0.85rem; background: var(--bg-body); color: var(--text-secondary); outline: none;">
                                    <option value="">Tandai tantangan... (Opsional)</option>
                                    @foreach($activeChallenges as $ac)
                                        <option value="{{ $ac->id }}">🎯 {{ $ac->judul }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <!-- Image Preview Container -->
                        <div id="imagePreviewContainer" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;"></div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 15px;">
                    <span id="fileCount" style="font-size: 0.85rem; color: var(--primary); font-weight: 600;"></span>
                    <button type="submit" class="btn btn-community" style="border-radius: 6px; padding: 6px 20px; font-weight: 700; font-size: 0.9rem;">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.querySelector('input[name="gambar[]"]');
            const previewContainer = document.getElementById('imagePreviewContainer');
            const fileCountText = document.getElementById('fileCount');
            let accumulatedFiles = new DataTransfer();

            fileInput.addEventListener('change', function() {
                for (let i = 0; i < this.files.length; i++) {
                    accumulatedFiles.items.add(this.files[i]);
                    
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        let imgWrapper = document.createElement('div');
                        imgWrapper.style.position = 'relative';
                        
                        let img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.height = '80px';
                        img.style.width = '80px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '8px';
                        img.style.border = '1px solid #ddd';
                        
                        imgWrapper.appendChild(img);
                        previewContainer.appendChild(imgWrapper);
                    }
                    reader.readAsDataURL(this.files[i]);
                }
                
                // Update input dengan file yang sudah diakumulasi
                fileInput.files = accumulatedFiles.files;
                fileCountText.textContent = accumulatedFiles.files.length + ' Foto dipilih';
            });
        });
    </script>

    <!-- Posts Feed -->
    @if($posts->count() > 0)
        @foreach($posts as $post)
            <div class="post-card fade-in-up {{ $post->is_pinned ? 'pinned-post' : '' }}" style="border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); margin-bottom: 20px; border: 1px solid var(--border-color); background: var(--bg-card);">
                @if($post->is_pinned)
                    <div class="text-xs mb-sm" style="color: var(--community-theme); font-weight: bold; padding: 0 20px; margin-top: 15px;">📌 Postingan Disematkan</div>
                @endif
                <div class="post-header" style="align-items: center; padding: {{ $post->is_pinned ? '5px 20px 15px' : '20px 20px 15px' }}; border-bottom: none;">
                    <div class="d-flex gap-sm align-center">
                        @if($post->user && $post->user->foto_profil)
                            <img src="{{ BookVerseHelper::uploadUrl('profiles', $post->user->foto_profil) }}" alt="" class="post-avatar" style="width:45px; height:45px; object-fit: cover;">
                        @else
                            <div class="post-avatar placeholder-img" style="border-radius:50%;font-size:1.2rem;width:45px;height:45px;">👤</div>
                        @endif
                        <div class="post-meta">
                            <div class="username" style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">
                                {{ $post->user->username ?? 'Unknown' }}
                                @if($post->user && $post->user->lencana && $post->user->lencana->isNotEmpty())
                                    <span style="font-size: 0.9em; margin-left: 4px;">
                                        @foreach($post->user->lencana as $ul)
                                            <span title="{{ $ul->lencana->nama_lencana }}">{{ $ul->lencana->ikon }}</span>
                                        @endforeach
                                    </span>
                                @endif
                            </div>
                            <div class="time" style="font-size: 0.8rem; color: var(--text-muted);">{{ BookVerseHelper::timeAgo($post->created_at) }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-sm">
                        @php
                            $canDelete = (auth()->id() === (int)$post->user_id || (auth()->check() && auth()->user()->isSuperadmin()) || ($community->creator_id == auth()->id()));
                            $isCommunityAdmin = (auth()->id() === $community->creator_id);
                        @endphp

                        <!-- Report Form -->
                        @if(auth()->id() !== (int)$post->user_id)
                            <form method="POST" action="{{ route('community.report') }}" class="inline-form" onsubmit="const r = prompt('Alasan melapor:'); if(r){ this.alasan.value = r; return true; } return false;">
                                @csrf
                                <input type="hidden" name="komunitas_id" value="{{ $community->id }}">
                                <input type="hidden" name="postingan_id" value="{{ $post->id }}">
                                <input type="hidden" name="alasan" value="">
                                <button type="submit" class="btn btn-ghost btn-sm" title="Laporkan" style="color:var(--warning); padding:6px; border-radius:20px; display:flex; align-items:center; gap:4px;"><span style="font-size:1.1rem;">🚩</span> Laporkan ke Admin</button>
                            </form>
                        @endif

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

                <div style="padding: 0 20px 15px;">
                    @if($post->judul)
                        <a href="{{ url('/community/post/' . $post->id) }}" class="post-title" style="display:block;color:var(--text-primary);text-decoration:none;font-size:1.2rem;font-weight:700;margin-bottom:10px;">
                            {{ $post->judul }}
                        </a>
                    @endif

                    @if($post->tantangan_id && $post->tantangan)
                        <a href="{{ route('community.challenge.show', ['id' => $community->id, 'challengeId' => $post->tantangan_id]) }}" style="display: inline-block; background: rgba(139, 92, 246, 0.1); color: var(--community-theme); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-bottom: 12px; text-decoration: none;">
                            🎯 Terkait Tantangan: {{ $post->tantangan->judul }}
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
                    @php
                        $userLiked = auth()->check() ? $post->likes->contains('user_id', auth()->id()) : false;
                    @endphp
                    <form method="POST" action="{{ route('community.post.like', $post->id) }}" style="flex:1;">
                        @csrf
                        <button type="submit" class="post-action-btn" style="width:100%; justify-content:center; color: {{ $userLiked ? 'var(--primary)' : 'var(--text-secondary)' }}; font-weight: 600; border-radius: 8px; padding: 8px; transition: background 0.2s ease; background: {{ $userLiked ? 'rgba(139, 92, 246, 0.1)' : 'transparent' }}; border: none; cursor: pointer;">
                            <span style="margin-right: 8px;">{{ $userLiked ? '👍' : '🤍' }}</span> {{ $post->likes->count() > 0 ? $post->likes->count() . ' ' : '' }}Suka
                        </button>
                    </form>

                    <a href="{{ url('/community/post/' . $post->id) }}" class="post-action-btn" style="flex:1; justify-content:center; color: var(--text-secondary); font-weight: 600; border-radius: 8px; padding: 8px; transition: background 0.2s ease; text-decoration: none; display: flex; align-items: center;">
                        <span style="margin-right: 8px;">💬</span> {{ $post->comment_count }} Komentar
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state" style="background: white; border-radius: 12px; padding: 60px 20px; border: 1px solid var(--border-color);">
            <span class="emoji" style="font-size: 4rem;">💬</span>
            <h3 style="font-size: 1.4rem; margin-top: 15px;">Belum ada diskusi</h3>
            <p style="color: var(--text-secondary);">Jadilah yang pertama memulai obrolan seru di komunitas ini!</p>
        </div>
    @endif
</div>
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
