@php
    $isSuperadmin = (auth()->user()->role_id === 1);
    $currentPage  = $currentAdminPage ?? 'dashboard';
    $username     = auth()->user()->username ?? 'Admin';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Admin Panel' }} — BookVerse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📚</text></svg>">
    <style>
        /* ══ ADMIN LAYOUT OVERRIDE ══════════════════════════════════ */
        body { background: #f0eef8; }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .admin-sidebar {
            width: 240px;
            background: linear-gradient(180deg, #1a0f3c 0%, #2d1b69 60%, #3b1fa8 100%);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 4px 0 24px rgba(0,0,0,0.2);
        }
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

        /* Logo area */
        .admin-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            text-decoration: none;
        }
        .admin-logo-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .admin-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
        }
        .admin-logo-text span { color: #a78bfa; }
        .admin-logo-sub {
            font-size: 0.62rem;
            color: rgba(255,255,255,0.35);
            font-weight: 400;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Nav label */
        .admin-nav-section {
            padding: 16px 12px 4px;
        }
        .admin-nav-label {
            font-size: 0.62rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.3);
            padding: 0 10px;
            margin-bottom: 6px;
        }

        /* Nav item */
        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.855rem;
            font-weight: 500;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: all 0.2s;
            margin: 1px 0;
            position: relative;
        }
        .admin-nav-item:hover {
            color: white;
            background: rgba(255,255,255,0.08);
        }
        .admin-nav-item.active {
            color: white;
            background: rgba(167,139,250,0.2);
            border: 1px solid rgba(167,139,250,0.25);
        }
        .admin-nav-item.active .nav-icon {
            background: #7c3aed;
            box-shadow: 0 4px 12px rgba(124,58,237,0.4);
        }
        .admin-nav-item.danger { color: rgba(252,165,165,0.7); }
        .admin-nav-item.danger:hover { background: rgba(220,38,38,0.15); color: #fca5a5; }

        .nav-icon {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.07);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        /* Sidebar bottom */
        .admin-sidebar-bottom {
            margin-top: auto;
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        /* User badge in sidebar */
        .admin-user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.06);
            border-radius: 10px;
            margin-bottom: 8px;
        }
        .admin-user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #7c3aed, #4f3cc9);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }
        .admin-user-name { font-size: 0.82rem; font-weight: 600; color: white; }
        .admin-user-role { font-size: 0.68rem; color: #a78bfa; }

        /* ── Main content ── */
        .admin-main {
            flex: 1;
            margin-left: 240px;
            min-height: 100vh;
            background: #f0eef8;
        }

        /* Top header bar */
        .admin-topbar {
            background: white;
            border-bottom: 1px solid #e8e0f0;
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 8px rgba(79,60,201,0.06);
        }
        .admin-topbar-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1030;
        }
        .admin-topbar-breadcrumb {
            font-size: 0.78rem;
            color: #9b90a8;
            margin-top: 1px;
        }
        .admin-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .topbar-time {
            font-size: 0.78rem;
            color: #9b90a8;
        }

        /* Content area */
        .admin-content {
            padding: 32px;
        }

        /* Stat cards */
        .stat-card {
            background: white;
            border: 1px solid #e8e0f0;
            border-radius: 16px;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(79,60,201,0.1); }

        .stat-icon-wrap {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .si-purple { background: #ede9fb; }
        .si-blue   { background: #eff6ff; }
        .si-green  { background: #f0fdf4; }
        .si-amber  { background: #fffbeb; }
        .si-pink   { background: #fdf2f8; }
        .si-red    { background: #fef2f2; }

        .stat-value { font-size: 1.8rem; font-weight: 700; color: #1a1030; line-height: 1; }
        .stat-label { font-size: 0.78rem; color: #9b90a8; margin-top: 4px; }
        .stat-change {
            font-size: 0.72rem;
            font-weight: 600;
            margin-top: 3px;
        }
        .stat-change.up { color: #16a34a; }
        .stat-change.warn { color: #d97706; }

        /* Page header */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1030;
        }
        .page-header p {
            font-size: 0.82rem;
            color: #9b90a8;
            margin-top: 3px;
        }
    </style>
    @yield('styles')
</head>
<body>
<div class="admin-layout">

    {{-- ── SIDEBAR ──────────────────────────────────── --}}
    <aside class="admin-sidebar" id="adminSidebar">

        {{-- Logo --}}
        <a href="{{ url('/admin/superadmin') }}" class="admin-logo-wrap">
            <div class="admin-logo-icon">📚</div>
            <div>
                <div class="admin-logo-text">Book<span>Verse</span></div>
                <div class="admin-logo-sub">Admin Panel</div>
            </div>
        </a>

        {{-- Navigation --}}
        <div class="admin-nav-section">
            <div class="admin-nav-label">Menu Utama</div>

            @if($isSuperadmin)
            <a href="{{ url('/admin/superadmin') }}"
               class="admin-nav-item {{ $currentPage === 'dashboard' ? 'active' : '' }}">
                <div class="nav-icon">📊</div>
                Dashboard
            </a>
            <a href="{{ url('/admin/superadmin/books') }}"
               class="admin-nav-item {{ $currentPage === 'books' ? 'active' : '' }}">
                <div class="nav-icon">📖</div>
                Kelola Buku
            </a>
            <a href="{{ url('/admin/superadmin/users') }}"
               class="admin-nav-item {{ $currentPage === 'users' ? 'active' : '' }}">
                <div class="nav-icon">👥</div>
                Kelola Pengguna
            </a>
            <a href="{{ url('/admin/superadmin/communities') }}"
               class="admin-nav-item {{ $currentPage === 'communities' ? 'active' : '' }}">
                <div class="nav-icon">🏘️</div>
                Kelola Komunitas
            </a>
            <a href="{{ url('/admin/superadmin/preloved') }}"
               class="admin-nav-item {{ $currentPage === 'preloved' ? 'active' : '' }}">
                <div class="nav-icon">🏷️</div>
                Kelola Preloved
            </a>
            <a href="{{ url('/admin/superadmin/genres') }}"
               class="admin-nav-item {{ $currentPage === 'genres' ? 'active' : '' }}">
                <div class="nav-icon">🎨</div>
                Kelola Genre
            </a>
            @else
            <a href="{{ url('/admin/komunitas') }}"
               class="admin-nav-item {{ $currentPage === 'dashboard' ? 'active' : '' }}">
                <div class="nav-icon">📊</div>
                Dashboard
            </a>
            @php
                $myCommunity = \App\Models\Komunitas::where('creator_id', auth()->id())->where('status', 'aktif')->first();
            @endphp
            @if($myCommunity)
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/members') }}"
               class="admin-nav-item {{ $currentPage === 'members' ? 'active' : '' }}">
                <div class="nav-icon">👥</div>
                Anggota
            </a>
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/moderation') }}"
               class="admin-nav-item {{ $currentPage === 'moderation' ? 'active' : '' }}">
                <div class="nav-icon">🛡️</div>
                Moderasi
            </a>
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/reports') }}"
               class="admin-nav-item {{ $currentPage === 'reports' ? 'active' : '' }}" style="color: rgba(252,165,165,0.8);">
                <div class="nav-icon" style="color:var(--danger);">🚨</div>
                Laporan
            </a>
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/events') }}"
               class="admin-nav-item {{ $currentPage === 'events' ? 'active' : '' }}">
                <div class="nav-icon" style="color:var(--warning);">📅</div>
                Klub Buku & Event
            </a>
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/settings') }}"
               class="admin-nav-item {{ $currentPage === 'settings' ? 'active' : '' }}">
                <div class="nav-icon" style="color:var(--success);">🎨</div>
                Identitas & Tema
            </a>

            <div class="admin-nav-label" style="margin-top: 15px;">Fitur Premium</div>
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/badges') }}"
               class="admin-nav-item {{ $currentPage === 'badges' ? 'active' : '' }}">
                <div class="nav-icon" style="color:#eab308;">🏆</div>
                Lencana & Gamifikasi
            </a>
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/bookshelf') }}"
               class="admin-nav-item {{ $currentPage === 'bookshelf' ? 'active' : '' }}">
                <div class="nav-icon" style="color:#0ea5e9;">📚</div>
                Rak Buku Komunitas
            </a>
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/challenges') }}"
               class="admin-nav-item {{ $currentPage === 'challenges' ? 'active' : '' }}">
                <div class="nav-icon" style="color:#f97316;">🎯</div>
                Tantangan Membaca
            </a>
            <a href="{{ url('/admin/komunitas/' . $myCommunity->id . '/qna') }}"
               class="admin-nav-item {{ $currentPage === 'qna' ? 'active' : '' }}">
                <div class="nav-icon" style="color:#8b5cf6;">🎙️</div>
                Sesi Q&A
            </a>
            @endif
            @endif
        </div>

        {{-- Sidebar bottom --}}
        <div class="admin-sidebar-bottom">
            {{-- User info --}}
            <div class="admin-user-badge">
                <div class="admin-user-avatar">{{ strtoupper(substr($username, 0, 1)) }}</div>
                <div>
                    <div class="admin-user-name">{{ $username }}</div>
                    <div class="admin-user-role">{{ $isSuperadmin ? '⚡ Superadmin' : '🛡️ Admin Komunitas' }}</div>
                </div>
            </div>

            <a href="{{ url('/') }}" class="admin-nav-item">
                <div class="nav-icon">🏠</div>
                Lihat Website
            </a>
            <form method="POST" action="{{ $isSuperadmin ? route('superadmin.logout') : route('logout') }}" style="margin-top:2px;">
                @csrf
                <button type="submit" class="admin-nav-item danger" style="width:100%;background:none;border:none;cursor:pointer;font-family:inherit;text-align:left;">
                    <div class="nav-icon" style="background:rgba(220,38,38,0.1);">🚪</div>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN ──────────────────────────────────────── --}}
    <div class="admin-main">

        {{-- Top bar --}}
        <div class="admin-topbar">
            <div>
                <div class="admin-topbar-title">
                    @php
                        $titles = [
                            'dashboard'   => 'Dashboard',
                            'books'       => 'Kelola Buku',
                            'users'       => 'Kelola Pengguna',
                            'communities' => 'Kelola Komunitas',
                            'preloved'    => 'Kelola Preloved',
                        ];
                    @endphp
                    {{ $titles[$currentPage] ?? ucfirst($currentPage) }}
                </div>
                <div class="admin-topbar-breadcrumb">BookVerse Admin / {{ $titles[$currentPage] ?? $currentPage }}</div>
            </div>
            <div class="admin-topbar-actions">
                <span class="topbar-time" id="topbarTime"></span>
                <a href="{{ url('/') }}" class="btn btn-ghost btn-sm" style="font-size:0.78rem;">← Ke Website</a>
            </div>
        </div>

        {{-- Alerts --}}
        <div style="padding: 16px 32px 0;">
            @if(session('success'))
            <div class="alert alert-success">
                <span>✅</span>
                <span>{!! session('success') !!}</span>
                <button class="close-alert" onclick="this.parentElement.remove()">✕</button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-error">
                <span>❌</span>
                <span>{!! session('error') !!}</span>
                <button class="close-alert" onclick="this.parentElement.remove()">✕</button>
            </div>
            @endif
        </div>

        {{-- Page content --}}
        <div class="admin-content">
            @yield('content')
        </div>
    </div>
</div>

<script>
    // Live clock in topbar
    function updateTime() {
        const el = document.getElementById('topbarTime');
        if (el) {
            const now = new Date();
            el.textContent = now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
        }
    }
    updateTime();
    setInterval(updateTime, 1000);
</script>
<script>const BASE_URL = '{{ url("/") }}/';</script>
<script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
@yield('scripts')
</body>
</html>
