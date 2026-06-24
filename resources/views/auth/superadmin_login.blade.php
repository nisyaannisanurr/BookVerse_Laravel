<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Login — BookVerse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #060412;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            pointer-events: none;
        }
        .blob-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, #4f3cc9 0%, transparent 70%);
            top: -20%; left: -15%;
            animation: blobFloat1 12s ease-in-out infinite;
        }
        .blob-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #7c1fa3 0%, transparent 70%);
            bottom: -15%; right: -10%;
            animation: blobFloat2 10s ease-in-out infinite;
        }
        .blob-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #1a3b8f 0%, transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: blobFloat3 14s ease-in-out infinite;
        }
        @keyframes blobFloat1 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(50px,40px) scale(1.06)} }
        @keyframes blobFloat2 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-40px,-50px) scale(1.08)} }
        @keyframes blobFloat3 { 0%,100%{transform:translate(-50%,-50%) scale(1)} 50%{transform:translate(-50%,-50%) scale(1.15)} }

        /* Grid */
        .grid-overlay {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(79,60,201,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79,60,201,0.07) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
        }

        /* Noise texture */
        .noise {
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none; opacity: 0.4;
        }

        .login-wrapper {
            position: relative; z-index: 10;
            width: 100%; max-width: 440px;
            padding: 24px;
            animation: fadeIn 0.6s ease;
        }
        @keyframes fadeIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

        /* Top badge */
        .top-badge {
            display: flex; align-items: center; justify-content: center;
            gap: 8px; margin-bottom: 24px;
        }
        .badge-pill {
            background: rgba(124,58,237,0.15);
            border: 1px solid rgba(124,58,237,0.35);
            color: #c4b5fd;
            font-size: 0.7rem; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase;
            padding: 6px 16px; border-radius: 999px;
            display: flex; align-items: center; gap: 6px;
        }
        .badge-dot {
            width: 6px; height: 6px;
            background: #7c3aed; border-radius: 50%;
            box-shadow: 0 0 8px rgba(124,58,237,0.8);
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.6;transform:scale(0.85)} }

        /* Card */
        .login-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 28px;
            padding: 44px;
            backdrop-filter: blur(24px);
            box-shadow:
                0 30px 80px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(255,255,255,0.08),
                0 0 0 1px rgba(124,58,237,0.1);
        }

        /* Logo */
        .logo-area { text-align: center; margin-bottom: 36px; }
        .logo-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #3d1fa3, #7c3aed);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 16px;
            box-shadow: 0 8px 32px rgba(79,60,201,0.5), 0 0 0 1px rgba(255,255,255,0.1);
        }
        .logo-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem; font-weight: 700; color: white; margin-bottom: 4px;
        }
        .logo-title span { color: #a78bfa; }
        .logo-sub { font-size: 0.78rem; color: rgba(255,255,255,0.35); letter-spacing: 0.02em; }

        /* Divider */
        .card-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
            margin-bottom: 28px;
        }

        /* Alerts */
        .alert-error {
            background: rgba(220,38,38,0.12);
            border: 1px solid rgba(220,38,38,0.25);
            color: #fca5a5;
            padding: 12px 16px; border-radius: 12px;
            font-size: 0.845rem; margin-bottom: 22px;
            display: flex; align-items: flex-start; gap: 8px;
        }
        .alert-success {
            background: rgba(22,163,74,0.12);
            border: 1px solid rgba(22,163,74,0.25);
            color: #86efac;
            padding: 12px 16px; border-radius: 12px;
            font-size: 0.845rem; margin-bottom: 22px;
            display: flex; align-items: flex-start; gap: 8px;
        }

        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block; font-size: 0.78rem; font-weight: 500;
            color: rgba(255,255,255,0.55); margin-bottom: 8px; letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            font-size: 1rem; pointer-events: none;
        }
        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            padding: 13px 16px 13px 42px;
            outline: none;
            transition: all 0.25s;
        }
        .form-input:focus {
            border-color: rgba(124,58,237,0.6);
            background: rgba(124,58,237,0.08);
            box-shadow: 0 0 0 4px rgba(124,58,237,0.12);
        }
        .form-input::placeholder { color: rgba(255,255,255,0.2); }

        /* Submit */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #4f3cc9 0%, #7c3aed 100%);
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 0.92rem; font-weight: 600;
            padding: 14px;
            border: none; border-radius: 14px; cursor: pointer;
            margin-top: 8px;
            transition: all 0.25s;
            box-shadow: 0 4px 20px rgba(79,60,201,0.45), 0 0 0 1px rgba(255,255,255,0.08);
            position: relative; overflow: hidden;
            letter-spacing: 0.01em;
        }
        .btn-login::before {
            content: '';
            position: absolute; top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
            transition: 0.6s;
        }
        .btn-login:hover::before { left: 100%; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(79,60,201,0.55); }
        .btn-login:active { transform: translateY(0); }

        /* Footer links */
        .back-link {
            text-align: center; margin-top: 28px;
        }
        .back-link a {
            font-size: 0.8rem; color: rgba(255,255,255,0.28);
            text-decoration: none; transition: 0.2s;
        }
        .back-link a:hover { color: rgba(255,255,255,0.55); }

        .security-info {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 14px;
            font-size: 0.7rem; color: rgba(255,255,255,0.18);
            letter-spacing: 0.02em;
        }
    </style>
</head>
<body>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>
<div class="grid-overlay"></div>
<div class="noise"></div>

<div class="login-wrapper">
    <div class="top-badge">
        <div class="badge-pill">
            <span class="badge-dot"></span>
            🔐 Panel Superadmin
        </div>
    </div>

    <div class="login-card">
        <div class="logo-area">
            <div class="logo-icon">📚</div>
            <div class="logo-title">Book<span>Verse</span></div>
            <div class="logo-sub">Akses khusus · Administrator Sistem</div>
        </div>

        <div class="card-divider"></div>

        @if(session('error'))
        <div class="alert-error">⚠️ {{ session('error') }}</div>
        @endif

        @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('superadmin.login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email Superadmin</label>
                <div class="input-wrap">
                    <span class="input-icon">✉️</span>
                    <input type="email" id="email" name="email" class="form-input"
                           placeholder="superadmin@bookverse.com"
                           value="{{ old('email') }}" required autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">🔑</span>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="••••••••••••" required autocomplete="off">
                </div>
            </div>

            <div class="form-group" style="margin-top:20px;">
                <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-theme="dark"></div>
                @error('g-recaptcha-response')
                    <span style="color:#fca5a5;font-size:0.8rem;margin-top:5px;display:block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-login">
                Masuk ke Panel Admin →
            </button>
        </form>
    </div>

    <div class="back-link">
        <a href="{{ url('/') }}">← Kembali ke BookVerse</a>
    </div>

    <div class="security-info">
        🔒 Halaman ini hanya untuk administrator sistem
    </div>
</div>
</body>
</html>
