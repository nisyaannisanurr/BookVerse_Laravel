<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Mitra — BookVerse</title>
    <link rel="icon" href="{{ asset('images/logo-bookverse.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <style>
        .login-hero {
            background: linear-gradient(135deg, #ede9fb 0%, #f5f0f0 60%, #fdf3e3 100%);
            padding: var(--space-2xl) 0;
            margin-bottom: 0;
            border-bottom: 1px solid var(--border);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 40px 36px;
            box-shadow: var(--shadow-md);
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
        }
        .login-card-header { margin-bottom: var(--space-lg); text-align: center; }
        .login-card-title {
            font-family: var(--font-display);
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }
        .login-card-subtitle { font-size: 0.9rem; color: var(--text-muted); }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            background: var(--bg);
            border: 1px solid var(--border);
            font-size: 0.95rem;
            outline: none;
        }
        .form-input:focus {
            background: var(--bg-white);
            border-color: var(--primary);
        }

        .btn-login {
            width: 100%; padding: 14px;
            background: var(--primary);
            color: white;
            font-size: 1rem; font-weight: 700;
            border: none; border-radius: var(--radius-md);
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 16px rgba(79,60,201,0.3);
        }
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .login-footer a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .login-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<nav class="navbar" id="mainNavbar">
    <div class="navbar-inner">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ asset('images/logo-bookverse.png') }}" alt="BookVerse" class="logo-img">
            <div class="logo-text">
                <span class="logo-name">Book<span>Verse</span></span>
                <span class="logo-tagline">Mitra Donasi</span>
            </div>
        </a>
        <div class="nav-actions" style="margin-left:auto;">
            <a href="{{ url('/mitra/daftar') }}" class="btn btn-outline btn-sm">Daftar Mitra</a>
        </div>
    </div>
</nav>

<div class="page-wrapper">
    <section class="login-hero">
        <div class="login-card">
            <div class="login-card-header">
                <div style="font-size: 3rem; margin-bottom: 10px;">🤝</div>
                <div class="login-card-title">Masuk Sebagai Mitra</div>
                <div class="login-card-subtitle">Masuk untuk melihat dan mengelola donasi buku untuk instansi Anda</div>
            </div>

            @if(session('error'))
                <div class="alert alert-error" style="margin-bottom: 16px;">❌ {!! session('error') !!}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 16px;">✅ {{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ url('/mitra/masuk') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email Instansi</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="email@instansi.com" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password kamu" required>
                </div>

                <button type="submit" class="btn-login">Masuk ke Dashboard</button>
            </form>

            <div class="login-footer">
                Belum mendaftar sebagai mitra?
                <a href="{{ url('/mitra/daftar') }}">Daftar di sini</a>
            </div>
        </div>
    </section>
</div>

</body>
</html>
