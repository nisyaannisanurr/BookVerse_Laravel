<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Daftar — BookVerse' }}</title>
    <meta name="description" content="Daftar gratis di BookVerse dan mulai petualangan membacamu bersama ribuan pecinta buku Indonesia.">
    <link rel="icon" href="{{ asset('images/logo-bookverse.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <style>
        /* ── Register Hero (same as login-hero) ── */
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
            align-items: start;
        }

        /* ── Left Side Copy ── */
        .login-left { animation: fadeInUp 0.5s ease forwards; padding-top: var(--space-md); }
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

        /* Step pills */
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
        .login-feature-icon {
            width: 32px; height: 32px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem; font-weight: 700;
            color: var(--primary);
            flex-shrink: 0;
        }
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
            padding: 36px 36px;
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
            margin: 18px 0;
            color: var(--text-muted);
            font-size: 0.78rem;
        }
        .auth-divider::before, .auth-divider::after {
            content: ''; flex: 1;
            height: 1px; background: var(--border);
        }

        /* Form */
        .login-form .form-group { margin-bottom: 14px; }
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
        .login-form .form-input:focus { background: var(--bg-white); }

        /* Password wrap */
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

        /* Strength bar */
        #strengthBar {
            height: 4px; border-radius: 2px;
            background: var(--border); overflow: hidden; margin-top: 6px;
        }
        #strengthFill {
            height: 100%; width: 0; border-radius: 2px;
            transition: width .3s, background .3s;
        }
        #pwReqs {
            list-style: none; padding: 0; margin: 8px 0 0;
            display: flex; flex-wrap: wrap; gap: 5px;
        }
        #pwReqs li {
            font-size: 0.71rem; padding: 2px 8px;
            border-radius: var(--radius-full);
            background: var(--bg); color: var(--text-muted);
            border: 1px solid var(--border);
            transition: var(--transition);
        }

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

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: var(--space-md);
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .login-footer a { color: var(--primary); font-weight: 600; }
        .login-footer a:hover { color: var(--primary-dark); text-decoration: underline; }

        /* ── Motivational Quote ── */
        .quote-block {
            position: relative;
            margin: var(--space-lg) 0;
            padding: 20px 20px 20px 24px;
            background: linear-gradient(135deg, rgba(79,60,201,0.07) 0%, rgba(139,92,246,0.04) 100%);
            border-left: 3px solid var(--primary);
            border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
            animation: fadeInUp 0.6s 0.3s ease both;
        }
        .quote-mark {
            font-size: 3rem;
            line-height: 1;
            color: var(--primary);
            opacity: 0.25;
            font-family: Georgia, serif;
            position: absolute;
            top: 6px;
            left: 14px;
        }
        .quote-text {
            font-size: 0.95rem;
            font-style: italic;
            color: var(--text-secondary);
            line-height: 1.7;
            padding-left: 18px;
            margin: 0 0 10px;
        }
        .quote-author {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 0.04em;
            padding-left: 18px;
            text-transform: uppercase;
        }

        /* Stats strip */
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

        /* Responsive */
        @media (max-width: 900px) {
            .login-hero-inner { grid-template-columns: 1fr; gap: var(--space-xl); }
            .login-left { order: 2; padding-top: 0; }
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
            <a href="{{ url('/login') }}" class="btn btn-outline btn-sm">Masuk</a>
        </div>
    </div>
</nav>



<!-- ═══════════════════════════════════════
     HERO — Register
     ═══════════════════════════════════════ -->
<div class="page-wrapper">
<section class="login-hero">
    <div class="login-hero-inner">

        <!-- LEFT: Branding copy + step pills -->
        <div class="login-left">
            <div class="login-tagline">Gabung Sekarang</div>
            <h1 class="login-title">
                Buat Akun <em>BookVerse</em>
            </h1>
            <p class="login-subtitle">
                Gratis selamanya. Bergabunglah dengan ribuan pecinta buku dan nikmati semua fitur — komunitas, rekomendasi, ulasan, dan buku bekas favoritmu.
            </p>

            <?php
            $quotes = [
                ["Seorang pembaca hidup seribu kehidupan sebelum ia mati. Orang yang tidak pernah membaca hanya hidup sekali.", "George R.R. Martin"],
                ["Buku adalah teman yang paling setia.", "Ernest Hemingway"],
                ["Semakin banyak kamu membaca, semakin banyak hal yang akan kamu ketahui.", "Dr. Seuss"],
                ["Sebuah buku adalah mimpi yang kamu pegang di tanganmu.", "Neil Gaiman"],
                ["Buku adalah cermin jiwa.", "Virginia Woolf"],
                ["Membaca adalah berlatih menjadi orang lain.", "Umberto Eco"],
                ["Bacalah, karena membaca adalah jendela dunia.", "R.A. Kartini"],
                ["Tidak ada teman yang sepati buku.", "Thomas Jefferson"],
                ["Buku adalah investasi terbaik yang bisa kamu lakukan untuk dirimu sendiri.", "Benjamin Franklin"],
                ["Perpustakaan adalah tempat di mana mimpi-mimpi dimulai.", "Jorge Luis Borges"],
            ];
            $q = $quotes[array_rand($quotes)];
            ?>
            <div class="quote-block">
                <span class="quote-mark">&ldquo;</span>
                <p class="quote-text">{{ $q[0] }}</p>
                <div class="quote-author">— {{ $q[1] }}</div>
            </div>

            <div class="login-features stagger-in">
                <div class="login-feature">
                    <div class="login-feature-icon">1</div>
                    <div class="login-feature-text">
                        <strong>Buat Akun Gratis</strong>
                        <span>Daftar dalam hitungan detik, tanpa biaya</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon">2</div>
                    <div class="login-feature-text">
                        <strong>Jelajahi Ribuan Buku</strong>
                        <span>Temukan bacaan favorit dari berbagai genre</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon">3</div>
                    <div class="login-feature-text">
                        <strong>Bergabung Komunitas</strong>
                        <span>Diskusi seru bersama sesama pecinta buku</span>
                    </div>
                </div>
                <div class="login-feature">
                    <div class="login-feature-icon">4</div>
                    <div class="login-feature-text">
                        <strong>Jual & Beli Preloved</strong>
                        <span>Transaksi buku bekas yang mudah dan aman</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Register Form Card -->
        <div class="login-right">
            <div class="login-card">
                <div class="login-card-header">
                    <div class="login-card-title">Buat Akun Baru ✨</div>
                    <div class="login-card-subtitle">Daftar gratis dan mulai petualangan membacamu</div>
                </div>

                {{-- Flash Messages --}}
                @if(session('error'))
                    <div class="alert alert-error">❌ {!! session('error') !!}</div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">✅ {{ session('success') }}</div>
                @endif

                {{-- Google Register --}}
                <a href="{{ route('auth.google') }}" class="btn-google" id="btn-google-register">
                    <svg width="20" height="20" viewBox="0 0 48 48" fill="none">
                        <path d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" fill="#FFC107"/>
                        <path d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" fill="#FF3D00"/>
                        <path d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" fill="#4CAF50"/>
                        <path d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" fill="#1976D2"/>
                    </svg>
                    Daftar dengan Google
                </a>

                <div class="auth-divider">atau daftar dengan email</div>

                <form method="POST" action="{{ url('/register') }}" class="login-form">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <input
                            type="text" id="username" name="username"
                            class="form-input"
                            placeholder="username_kamu"
                            value="{{ old('username') }}"
                            required minlength="3" maxlength="50"
                            pattern="[a-zA-Z0-9_]+"
                            title="Hanya huruf, angka, dan underscore"
                            autofocus
                        >
                        <span class="form-hint">3–50 karakter · huruf, angka, underscore</span>
                        @error('username')
                            <span class="form-hint" style="color:var(--danger);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email</label>
                        <input
                            type="email" id="email" name="email"
                            class="form-input"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')
                            <span class="form-hint" style="color:var(--danger);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="pw-wrap">
                            <input
                                type="password" id="password" name="password"
                                class="form-input"
                                placeholder="Min. 8 karakter"
                                required minlength="8"
                                oninput="checkStrength(this.value)"
                                style="padding-right:42px;"
                            >
                            <button type="button" class="pw-toggle" id="pwToggle1" title="Tampilkan password">👁</button>
                        </div>
                        <div id="strengthBar"><div id="strengthFill"></div></div>
                        <ul id="pwReqs">
                            <li id="req-len">✗ Min. 8 karakter</li>
                            <li id="req-up">✗ Huruf kapital</li>
                            <li id="req-low">✗ Huruf kecil</li>
                            <li id="req-num">✗ Angka</li>
                        </ul>
                        @error('password')
                            <span class="form-hint" style="color:var(--danger);">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Konfirmasi Password</label>
                        <div class="pw-wrap">
                            <input
                                type="password" id="confirm_password" name="confirm_password"
                                class="form-input"
                                placeholder="Ulangi password kamu"
                                required
                                oninput="checkMatch()"
                                style="padding-right:42px;"
                            >
                            <button type="button" class="pw-toggle" id="pwToggle2" title="Tampilkan password">👁</button>
                        </div>
                        <span id="matchMsg" style="font-size:0.75rem;margin-top:5px;display:block;"></span>
                    </div>

                    <button type="submit" class="btn-login" id="btn-submit-register">
                        Daftar Sekarang →
                    </button>
                </form>

                <div class="login-footer">
                    Sudah punya akun?
                    <a href="{{ url('/login') }}">Masuk di sini</a>
                </div>
            </div>
        </div>

    </div>
</section>

</div>



<script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
<script>
    // Password toggles
    function setupToggle(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const inp = document.getElementById(inputId);
        if (btn && inp) {
            btn.addEventListener('click', () => {
                const isText = inp.type === 'text';
                inp.type = isText ? 'password' : 'text';
                btn.textContent = isText ? '👁' : '🙈';
            });
        }
    }
    setupToggle('pwToggle1', 'password');
    setupToggle('pwToggle2', 'confirm_password');

    // Password strength
    function req(id, pass) {
        var el = document.getElementById(id);
        if (!el) return;
        if (pass) {
            el.style.background = '#dcfce7';
            el.style.color = '#16a34a';
            el.style.borderColor = '#bbf7d0';
            el.textContent = el.textContent.replace('✗','✓');
        } else {
            el.style.background = 'var(--bg)';
            el.style.color = 'var(--text-muted)';
            el.style.borderColor = 'var(--border)';
            el.textContent = el.textContent.replace('✓','✗');
        }
    }
    function checkStrength(v) {
        var len = v.length >= 8, up = /[A-Z]/.test(v), low = /[a-z]/.test(v), num = /[0-9]/.test(v);
        req('req-len', len); req('req-up', up); req('req-low', low); req('req-num', num);
        var score = [len, up, low, num].filter(Boolean).length;
        var colors = ['', '#ef4444', '#f97316', '#eab308', '#22c55e'];
        var widths  = ['0%', '25%', '50%', '75%', '100%'];
        var fill = document.getElementById('strengthFill');
        if (fill) { fill.style.width = widths[score]; fill.style.background = colors[score]; }
    }
    function checkMatch() {
        var pw  = document.getElementById('password').value;
        var cp  = document.getElementById('confirm_password').value;
        var msg = document.getElementById('matchMsg');
        if (!cp) { msg.textContent = ''; return; }
        if (pw === cp) { msg.textContent = '✓ Password cocok'; msg.style.color = 'var(--success)'; }
        else           { msg.textContent = '✗ Password tidak cocok'; msg.style.color = 'var(--danger)'; }
    }

    // Navbar scroll
    const navbar = document.getElementById('mainNavbar');
    window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 10));

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
