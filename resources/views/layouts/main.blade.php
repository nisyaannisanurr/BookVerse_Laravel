<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BookVerse — Buka Buku, Buka Dunia')</title>
    <meta name="description" content="@yield('meta_description', 'BookVerse adalah komunitas pecinta buku untuk membaca, berbagi rekomendasi, dan menemukan dunia baru bersama.')">
    <link rel="icon" href="{{ asset('images/logo-bookverse.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    @stack('styles')
</head>
<body>

<!-- ═══════════════════════════════════════
     TOP NAVBAR
     ═══════════════════════════════════════ -->
<nav class="navbar" id="mainNavbar">
    <div class="navbar-inner">

        <!-- Logo -->
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('images/logo-bookverse.png') }}" alt="BookVerse" class="logo-img">
            <div class="logo-text">
                <span class="logo-name">Book<span>Verse</span></span>
                <span class="logo-tagline">Buka Buku, Buka Dunia.</span>
            </div>
        </a>

        <!-- Center Nav Links -->
        <div class="nav-links" id="navLinks">
            <a href="{{ url('/') }}"                class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
            <a href="{{ url('/community') }}"        class="nav-link {{ request()->routeIs('community.*') ? 'active' : '' }}">Komunitas</a>
            <a href="{{ url('/recommendations') }}"  class="nav-link {{ request()->routeIs('recommendations.*') || request()->routeIs('books.*') || request()->routeIs('search') ? 'active' : '' }}">Rekomendasi</a>
            <a href="{{ url('/preloved') }}"          class="nav-link {{ request()->routeIs('preloved.*') ? 'active' : '' }}">Preloved Books</a>
            <a href="{{ url('/about') }}"             class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">Tentang Kami</a>
        </div>

        <!-- Right Actions -->
        <div class="nav-actions">
            <!-- Search -->
            <button class="nav-search-btn" id="searchToggle" title="Cari">🔍</button>

            @auth
                <!-- Notifications -->
                <a href="{{ url('/notifications') }}" class="notif-btn" id="notifBtn" title="Notifikasi">
                    🔔
                    <span class="notif-badge" id="notifBadge"></span>
                </a>

                <!-- User -->
                <a href="{{ url('/profile') }}" class="nav-user">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('uploads/profiles/'.auth()->user()->foto_profil) }}" alt="" class="nav-user-avatar">
                    @else
                        <div class="nav-user-avatar" style="background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:0.9rem;">👤</div>
                    @endif
                    <span class="nav-user-name">{{ auth()->user()->username }}</span>
                </a>

                @if(auth()->user()->isSuperadmin())
                    <a href="{{ url('/admin/superadmin') }}" class="btn btn-outline btn-sm">Admin</a>
                @elseif(auth()->user()->isAdminKomunitas())
                    <a href="{{ url('/admin/komunitas') }}" class="btn btn-outline btn-sm">Dashboard</a>
                @endif

                <form method="POST" action="{{ url('/logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Keluar</button>
                </form>
            @else
                <a href="{{ url('/login') }}"    class="btn btn-outline btn-sm">Masuk</a>
                <a href="{{ url('/register') }}" class="btn btn-primary btn-sm">Daftar</a>
            @endauth

            <!-- Mobile hamburger -->
            <button class="hamburger" id="hamburgerBtn" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>

    </div>
</nav>

<!-- ═══════════════════════════════════════
     SEARCH OVERLAY
     ═══════════════════════════════════════ -->
<div class="search-overlay" id="searchOverlay">
    <form class="search-box" action="{{ url('/books') }}" method="GET">
        <span style="font-size:1.1rem;color:var(--text-muted);">🔍</span>
        <input type="text" name="search" placeholder="Cari buku, penulis, genre..." autocomplete="off" autofocus>
        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        <button type="button" class="btn btn-ghost btn-sm" id="searchClose">✕</button>
    </form>
</div>

<!-- ═══════════════════════════════════════
     MOBILE DRAWER
     ═══════════════════════════════════════ -->
<div class="search-overlay" id="mobileOverlay" style="align-items:flex-start;padding-top:0;">
    <div style="background:white;width:280px;min-height:100vh;padding:var(--space-lg);padding-top:80px;box-shadow:var(--shadow-lg);">
        <a href="{{ url('/') }}" class="logo" style="margin-bottom:var(--space-xl);display:flex;">
            <img src="{{ asset('images/logo-bookverse.png') }}" alt="" class="logo-img">
            <div class="logo-text">
                <span class="logo-name">Book<span>Verse</span></span>
                <span class="logo-tagline">Buka Buku, Buka Dunia.</span>
            </div>
        </a>
        <div style="display:flex;flex-direction:column;gap:4px;">
            <a href="{{ url('/') }}"             class="admin-nav-item {{ request()->is('/') ? 'active' : '' }}"><span class="icon">🏠</span> Beranda</a>
            <a href="{{ url('/community') }}"    class="admin-nav-item {{ request()->is('community*') ? 'active' : '' }}"><span class="icon">🏘️</span> Komunitas</a>
            <a href="{{ url('/recommendations') }}" class="admin-nav-item {{ request()->is('recommendations*') ? 'active' : '' }}"><span class="icon">✨</span> Rekomendasi</a>
            <a href="{{ url('/preloved') }}"     class="admin-nav-item {{ request()->is('preloved*') ? 'active' : '' }}"><span class="icon">🏷️</span> Preloved Books</a>
            <a href="{{ url('/about') }}"        class="admin-nav-item"><span class="icon">ℹ️</span> Tentang Kami</a>
            @auth
                <a href="{{ url('/profile') }}"  class="admin-nav-item"><span class="icon">👤</span> Profil</a>
                <a href="{{ url('/notifications') }}" class="admin-nav-item"><span class="icon">🔔</span> Notifikasi</a>
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-item w-full" style="border:none;background:none;cursor:pointer;color:var(--danger);"><span class="icon">🚪</span> Keluar</button>
                </form>
            @else
                <a href="{{ url('/login') }}"    class="admin-nav-item"><span class="icon">🔑</span> Masuk</a>
                <a href="{{ url('/register') }}" class="admin-nav-item"><span class="icon">✍️</span> Daftar</a>
            @endauth
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════
     FLASH MESSAGES
     ═══════════════════════════════════════ -->
@if(session('success') || session('error') || session('info'))
<div style="position:fixed;top:calc(var(--navbar-h) + 12px);left:50%;transform:translateX(-50%);z-index:900;min-width:320px;max-width:500px;">
    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}<button class="close-alert">✕</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}<button class="close-alert">✕</button></div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">ℹ️ {{ session('info') }}<button class="close-alert">✕</button></div>
    @endif
</div>
@endif

<!-- ═══════════════════════════════════════
     GLOBAL REPORT MODAL
     ═══════════════════════════════════════ -->
@auth
<div id="globalReportModal" class="search-overlay" style="align-items:center; justify-content:center; padding: 20px; z-index: 99999;">
    <div style="background:var(--bg-card); width:100%; max-width:500px; border-radius:24px; padding:30px; position:relative; box-shadow:0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <button type="button" onclick="closeReportModal()" style="position:absolute; top:20px; right:20px; background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        
        <h2 style="font-family:var(--font-display); font-size:1.5rem; color:var(--danger); margin-bottom:20px; display:flex; align-items:center; gap:10px;">
            <span style="font-size:1.8rem;">🚩</span> Laporkan Pelanggaran
        </h2>
        
        <form id="globalReportForm" action="{{ url('/report') }}" method="POST">
            @csrf
            <input type="hidden" name="tipe_entitas" id="modal-tipe_entitas" value="">
            <input type="hidden" name="entitas_id" id="modal-entitas_id" value="">
            
            <!-- User Info (Readonly) -->
            <div style="background:var(--bg-surface); padding:15px; border-radius:12px; border:1px solid var(--border); margin-bottom:20px; display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; justify-content:space-between; font-size:0.9rem;">
                    <span style="color:var(--text-muted);">Nama Pelapor:</span>
                    <strong style="color:var(--text);">{{ auth()->user()->username }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.9rem;">
                    <span style="color:var(--text-muted);">Email:</span>
                    <strong style="color:var(--text);">{{ auth()->user()->email }}</strong>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label style="display:block; font-weight:600; margin-bottom:8px; color:var(--text);">Alasan Utama <span style="color:var(--danger);">*</span></label>
                <select name="alasan" id="modal-alasan-select" class="form-control" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:12px; background:var(--bg-surface);" required>
                    <option value="" disabled selected>-- Pilih Alasan --</option>
                    <option value="Konten tidak pantas / Melanggar hukum">Konten tidak pantas / Melanggar hukum</option>
                    <option value="Penipuan / Spam">Penipuan / Spam</option>
                    <option value="Ujaran kebencian / Harassment">Ujaran kebencian / Pelecehan</option>
                    <option value="Pelanggaran hak cipta">Pelanggaran hak cipta</option>
                    <option value="Lainnya">Lainnya (Tulis detail di bawah)</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom:25px;">
                <label style="display:block; font-weight:600; margin-bottom:8px; color:var(--text);">Detail Tambahan (Opsional)</label>
                <textarea name="detail_tambahan" rows="3" class="form-control" placeholder="Jelaskan lebih spesifik apa yang terjadi..." style="width:100%; padding:12px; border:1px solid var(--border); border-radius:12px; resize:vertical; background:var(--bg-surface);"></textarea>
            </div>
            
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="closeReportModal()" class="btn btn-ghost" style="padding:10px 20px; border-radius:50px; font-weight:600;">Batal</button>
                <button type="submit" class="btn" style="background:var(--danger); color:white; padding:10px 24px; border-radius:50px; font-weight:bold; box-shadow:0 4px 14px rgba(220, 38, 38, 0.4);">Kirim Laporan</button>
            </div>
        </form>
    </div>
</div>
@endauth

<!-- ═══════════════════════════════════════
     PAGE CONTENT
     ═══════════════════════════════════════ -->
<div class="page-wrapper">
    @yield('hero')

    <main class="page-content">
        @yield('content')
    </main>
</div>

<!-- ═══════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════ -->
<footer style="background:var(--text);color:white;padding:var(--space-xl) 0;margin-top:var(--space-2xl);">
    <div style="max-width:var(--max-w);margin:0 auto;padding:0 var(--space-xl);display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:var(--space-xl);">
        <div>
            <div class="logo" style="margin-bottom:var(--space-md);">
                <img src="{{ asset('images/logo-bookverse.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;">
                <div>
                    <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;color:white;">BookVerse</div>
                    <div style="font-size:0.7rem;color:var(--accent);font-weight:500;">Buka Buku, Buka Dunia.</div>
                </div>
            </div>
            <p style="font-size:0.85rem;color:rgba(255,255,255,0.6);line-height:1.7;max-width:280px;">
                Komunitas pecinta buku untuk membaca, berbagi rekomendasi, dan menemukan dunia baru bersama.
            </p>
        </div>
        <div>
            <h4 style="font-size:0.85rem;font-weight:600;margin-bottom:var(--space-md);color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.08em;">Menu</h4>
            @foreach([['Beranda','/'],[' Buku','/books'],['Komunitas','/community'],['Preloved','/preloved']] as $item)
                <a href="{{ url($item[1]) }}" style="display:block;font-size:0.85rem;color:rgba(255,255,255,0.7);margin-bottom:6px;transition:var(--transition);"
                   onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">{{ $item[0] }}</a>
            @endforeach
        </div>
        <div>
            <h4 style="font-size:0.85rem;font-weight:600;margin-bottom:var(--space-md);color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.08em;">Akun</h4>
            @foreach([['Profil','/profile'],['Notifikasi','/notifications']] as $item)
                <a href="{{ url($item[1]) }}" style="display:block;font-size:0.85rem;color:rgba(255,255,255,0.7);margin-bottom:6px;transition:var(--transition);"
                   onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">{{ $item[0] }}</a>
            @endforeach
        </div>
        <div>
            <h4 style="font-size:0.85rem;font-weight:600;margin-bottom:var(--space-md);color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.08em;">Info</h4>
            @foreach([['Tentang Kami','/about'],['Rekomendasi','/recommendations']] as $item)
                <a href="{{ url($item[1]) }}" style="display:block;font-size:0.85rem;color:rgba(255,255,255,0.7);margin-bottom:6px;transition:var(--transition);"
                   onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">{{ $item[0] }}</a>
            @endforeach
        </div>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,0.1);margin-top:var(--space-xl);padding-top:var(--space-md);text-align:center;font-size:0.8rem;color:rgba(255,255,255,0.4);">
        © {{ date('Y') }} BookVerse. All rights reserved.
    </div>
</footer>

<script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
<script>
    function openReportModal(tipeEntitas, entitasId) {
        console.log("Opening report modal for: " + tipeEntitas + " " + entitasId);
        const modal = document.getElementById('globalReportModal');
        if (modal) {
            document.getElementById('modal-tipe_entitas').value = tipeEntitas;
            document.getElementById('modal-entitas_id').value = entitasId;
            modal.classList.add('open');
            document.body.style.overflow = 'hidden'; // prevent scrolling
        } else {
            console.error("Modal element not found!");
            alert("Terjadi kesalahan, modal tidak ditemukan.");
        }
    }
    
    function closeReportModal() {
        const modal = document.getElementById('globalReportModal');
        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
            document.getElementById('globalReportForm').reset();
        }
    }
    
    // Fallback for old prompt
    function submitReportPrompt(formId, inputId, message) {
        let reason = prompt(message);
        if (reason !== null) {
            if (reason.trim() === '') {
                alert('Alasan pelaporan tidak boleh kosong.');
            } else {
                document.getElementById(inputId).value = reason.trim();
                document.getElementById(formId).submit();
            }
        }
    }
</script>
@stack('scripts')
</body>
</html>
