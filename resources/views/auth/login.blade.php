<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Masuk — BookVerse' }}</title>
    <meta name="description" content="Masuk ke akun BookVerse dan mulai eksplorasi dunia buku bersama ribuan pembaca lainnya.">
    <link rel="icon" href="{{ asset('images/logo-bookverse.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <style>
        /* ── Login Hero ── */
        .login-hero {
            background: linear-gradient(135deg, #ede9fb 0%, #f5f0f0 60%, #fdf3e3 100%);
            padding: var(--space-2xl) 0;
            margin-bottom: 0;
            border-bottom: 1px solid var(--border);
        }
        .login-hero-inner {
            max-width: var(--max-w);
            margin: 0 auto;
            padding: 0 var(--space-xl);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-2xl);
            align-items: center;
        }

        /* ── Left Side Copy ── */
        .login-left { animation: fadeInUp 0.5s ease forwards; }
        .login-title {
            font-family: var(--font-display);
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
            margin-bottom: var(--space-md);
        }
        .login-title em { color: var(--primary); font-style: normal; }
        .login-tagline {
            font-size: 0.85rem;
            color: var(--accent);
            font-weight: 600;
            letter-spacing: 0.03em;
            margin-bottom: var(--space-md);
            display: flex; align-items: center; gap: var(--space-sm);
        }
        .login-tagline::before, .login-tagline::after {
            content: '—'; color: var(--accent); opacity: 0.5;
        }
        .login-subtitle {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: var(--space-xl);
        }

        /* Feature pills on left */
        .login-features { display: flex; flex-direction: column; gap: 10px; }
        .login-feature {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: 12px 16px;
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            transition: var(--transition);
            box-shadow: var(--shadow-xs);
        }
        .login-feature:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateX(4px);
            box-shadow: var(--shadow-card);
        }
        .login-feature-icon { font-size: 1.4rem; flex-shrink: 0; }
        .login-feature-text { flex: 1; }
        .login-feature-text strong {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 1px;
        }
        .login-feature-text span {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* ── Right: Form Card ── */
        .login-right {
            animation: fadeInUp 0.5s 0.1s ease both;
        }
        .login-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 40px 36px;
            box-shadow: var(--shadow-md);
        }
        .login-card-header { margin-bottom: var(--space-lg); }
        .login-card-title {
            font-family: var(--font-display);
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .login-card-subtitle { font-size: 0.875rem; color: var(--text-muted); }

        /* Google button */
        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 11px 20px;
            background: var(--bg-white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-md);
            font-family: var(--font-sans);
            font-size: 0.9rem; font-weight: 600; color: var(--text);
            cursor: pointer; text-decoration: none;
            transition: var(--transition);
            box-shadow: var(--shadow-xs);
        }
        .btn-google:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            box-shadow: var(--shadow-card);
            transform: translateY(-1px);
            color: var(--primary);
        }
        .btn-google:active { transform: translateY(0); }

        /* Divider */
        .auth-divider {
            display: flex; align-items: center; gap: 12px;
            margin: 20px 0;
            color: var(--text-muted);
            font-size: 0.78rem;
        }
        .auth-divider::before, .auth-divider::after {
            content: ''; flex: 1;
            height: 1px; background: var(--border);
        }

        /* Form overrides to match site style */
        .login-form .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-secondary);
            letter-spacing: 0.02em;
        }
        .login-form .form-input {
            padding: 11px 14px;
            border-radius: var(--radius-md);
            background: var(--bg);
        }
        .login-form .form-input:focus {
            background: var(--bg-white);
        }

        /* Password wrapper */
        .pw-wrap { position: relative; }
        .pw-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: var(--text-muted); cursor: pointer;
            font-size: 1rem; padding: 4px;
            transition: var(--transition);
        }
        .pw-toggle:hover { color: var(--primary); }

        /* Submit */
        .btn-login {
            width: 100%; padding: 12px;
            background: var(--primary);
            color: white;
            font-family: var(--font-sans);
            font-size: 0.92rem; font-weight: 600;
            border: none; border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            margin-top: var(--space-sm);
            box-shadow: 0 4px 16px rgba(79,60,201,0.3);
            position: relative; overflow: hidden;
        }
        .btn-login::after {
            content: '';
            position: absolute; top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: 0.5s;
        }
        .btn-login:hover::after { left: 100%; }
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(79,60,201,0.4);
        }
        .btn-login:active { transform: translateY(0); }

        /* Footer link */
        .login-footer {
            text-align: center;
            margin-top: var(--space-lg);
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .login-footer a {
            color: var(--primary);
            font-weight: 600;
        }
        .login-footer a:hover { color: var(--primary-dark); text-decoration: underline; }

        /* ── Stats strip below form ── */
        .login-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            border: 1px solid var(--border);
            margin-top: var(--space-2xl);
        }
        .login-stat {
            background: var(--text);
            padding: var(--space-lg) var(--space-md);
            text-align: center;
        }
        .login-stat-icon { font-size: 1.3rem; margin-bottom: 6px; }
        .login-stat-value { font-size: 1.1rem; font-weight: 700; color: white; }
        .login-stat-label { font-size: 0.72rem; color: rgba(255,255,255,0.5); margin-top: 2px; }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .login-hero-inner {
                grid-template-columns: 1fr;
                gap: var(--space-xl);
            }
            .login-left { order: 2; }
            .login-right { order: 1; }
            .login-title { font-size: 2rem; }
        }
        @media (max-width: 768px) {
            .login-hero { padding: var(--space-xl) 0; }
            .login-card { padding: 28px 20px; }
            .login-stats { grid-template-columns: repeat(2, 1fr); }
            .login-hero-inner { padding: 0 var(--space-md); }
        }
    </style>
</head>
<body>

<!-- ═══════════════════════════════════════
     NAVBAR (same as main layout)
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

        <!-- Right Actions -->
        <div class="nav-actions" style="margin-left:auto;">
            <a href="{{ url('/register') }}" class="btn btn-outline btn-sm">Daftar</a>
        </div>
    </div>
</nav>



<!-- ═══════════════════════════════════════
     HERO — Login
     ═══════════════════════════════════════ -->
<div class="page-wrapper">
<section class="login-hero">
    <div class="login-hero-inner">

        <!-- LEFT: Branding copy + feature pills -->
        <div class="login-left">
            <div class="login-tagline">Selamat Datang Kembali</div>
            <h1 class="login-title">
                Masuk ke <em>BookVerse</em>
            </h1>
            <p class="login-subtitle">
                Satu akun untuk semua fitur — komunitas, rekomendasi buku, ulasan, dan jual-beli buku bekas favoritmu.
            </p>

            <div class="login-features stagger-in">
                <div class="login-feature">
                    <div class="login-feature-icon">📖</div>
                    <div class="login-feature-text">
                        <strong>Koleksi Buku Lengkap</strong>
                        <span>Temukan ribuan judul dari berbagai genre</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon">🏘️</div>
                    <div class="login-feature-text">
                        <strong>Komunitas Pembaca</strong>
                        <span>Bergabung & diskusi bersama sesama pecinta buku</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon">⭐</div>
                    <div class="login-feature-text">
                        <strong>Review & Rekomendasi</strong>
                        <span>Baca ulasan jujur dan temukan buku selanjutnya</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon">🏷️</div>
                    <div class="login-feature-text">
                        <strong>Preloved Books</strong>
                        <span>Jual beli buku bekas dengan mudah & aman</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Login Form Card -->
        <div class="login-right">
            <div class="login-card">
                <div class="login-card-header">
                    <div class="login-card-title">Selamat Datang Kembali 👋</div>
                    <div class="login-card-subtitle">Pilih cara masuk yang kamu inginkan</div>
                </div>

                {{-- Flash Messages --}}
                @if(session('info'))
                    <div class="alert alert-info">💡 {{ session('info') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">❌ {!! session('error') !!}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">✅ {{ session('success') }}</div>
                @endif

                {{-- Google Login --}}
                <a href="{{ route('auth.google') }}" class="btn-google" id="btn-google-login">
                    <svg width="20" height="20" viewBox="0 0 48 48" fill="none">
                        <path d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" fill="#FFC107"/>
                        <path d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" fill="#FF3D00"/>
                        <path d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" fill="#4CAF50"/>
                        <path d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" fill="#1976D2"/>
                    </svg>
                    Masuk dengan Google
                </a>

                <div class="auth-divider">atau masuk dengan email</div>

                <form method="POST" action="{{ url('/login') }}" class="login-form">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                        @error('email')
                            <span class="form-hint" style="color:var(--danger);">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="pw-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Masukkan password kamu"
                                required
                                style="padding-right:42px;"
                            >
                            <button type="button" class="pw-toggle" id="pwToggle" title="Tampilkan/sembunyikan password">
                                👁
                            </button>
                        </div>
                        @error('password')
                            <span class="form-hint" style="color:var(--danger);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-top: 10px;">
                        <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response')
                            <span class="form-hint" style="color:var(--danger); margin-top:5px; display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login" id="btn-submit-login">
                        Masuk ke BookVerse →
                    </button>
                </form>

                <div class="login-footer">
                    Belum punya akun?
                    <a href="{{ url('/register') }}">Daftar Gratis</a>
                </div>
            </div>
        </div>

    </div>
</section>

</div>



<script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
<script>
    // Password toggle
    const pwToggle = document.getElementById('pwToggle');
    const pwInput  = document.getElementById('password');
    if (pwToggle && pwInput) {
        pwToggle.addEventListener('click', () => {
            const isText = pwInput.type === 'text';
            pwInput.type = isText ? 'password' : 'text';
            pwToggle.textContent = isText ? '👁' : '🙈';
        });
    }

    // Navbar scroll effect (reuse from main layout logic)
    const navbar = document.getElementById('mainNavbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 10);
    });

    // Search overlay
    const searchToggle  = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose   = document.getElementById('searchClose');
    if (searchToggle) searchToggle.addEventListener('click', () => searchOverlay.classList.add('open'));
    if (searchClose)  searchClose.addEventListener('click',  () => searchOverlay.classList.remove('open'));

    // Mobile hamburger
    const hamburgerBtn  = document.getElementById('hamburgerBtn');
    const mobileOverlay = document.getElementById('mobileOverlay');
    if (hamburgerBtn) hamburgerBtn.addEventListener('click', () => mobileOverlay.classList.toggle('open'));
    if (mobileOverlay) mobileOverlay.addEventListener('click', e => {
        if (e.target === mobileOverlay) mobileOverlay.classList.remove('open');
    });
</script>
</body>
</html>
