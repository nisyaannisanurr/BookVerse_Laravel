<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk sebagai Admin Komunitas — BookVerse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; background: #f5f3ff; }

        .auth-left {
            width: 44%;
            background: linear-gradient(145deg, #312e81 0%, #4f46e5 50%, #818cf8 100%);
            display: flex; flex-direction: column; justify-content: center;
            align-items: center; padding: 60px 48px; position: relative; overflow: hidden;
        }
        .auth-left::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 340px; height: 340px; background: rgba(255,255,255,0.06); border-radius: 50%;
        }
        .auth-left::after {
            content: ''; position: absolute; bottom: -70px; left: -60px;
            width: 280px; height: 280px; background: rgba(255,255,255,0.05); border-radius: 50%;
        }
        .left-content { position: relative; z-index: 2; text-align: center; }
        .left-logo {
            width: 80px; height: 80px; background: rgba(255,255,255,0.15);
            border-radius: 24px; display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem; margin: 0 auto 20px;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        .left-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);
            color: white; font-size: 0.72rem; font-weight: 700;
            letter-spacing: 0.06em; padding: 5px 14px; border-radius: 20px;
            margin-bottom: 16px; text-transform: uppercase;
        }
        .left-brand { font-family: 'Playfair Display', serif; font-size: 2.2rem; color: white; margin-bottom: 8px; }
        .left-tagline { font-size: 0.88rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 44px; }

        .left-perks { text-align: left; width: 100%; max-width: 280px; }
        .perk-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 12px 16px; margin-bottom: 10px;
            background: rgba(255,255,255,0.08); border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .perk-icon { font-size: 1.3rem; flex-shrink: 0; }
        .perk-text { font-size: 0.82rem; color: rgba(255,255,255,0.85); line-height: 1.4; }
        .perk-text strong { color: white; display: block; margin-bottom: 2px; }

        .auth-right {
            flex: 1; display: flex; align-items: center;
            justify-content: center; padding: 48px 40px; background: white;
        }
        .auth-form-wrap {
            width: 100%; max-width: 400px;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

        .auth-title { font-family: 'Playfair Display', serif; font-size: 1.85rem; color: #1e1b4b; margin-bottom: 6px; }
        .auth-subtitle { font-size: 0.875rem; color: #8b85c0; margin-bottom: 32px; }

        .alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:12px; font-size:0.845rem; margin-bottom:20px; line-height:1.5; }
        .alert-error   { background:#fff1f1; border:1px solid #fecaca; color:#c0392b; }
        .alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
        .alert-info    { background:#eef2ff; border:1px solid #c7d2fe; color:#3730a3; }

        .form-group { margin-bottom: 18px; }
        .form-label { display:block; font-size:0.8rem; font-weight:600; color:#3730a3; margin-bottom:7px; }
        .form-input {
            width:100%; padding:12px 16px;
            border:1.5px solid #e0e7ff; border-radius:12px;
            font-family:'Inter',sans-serif; font-size:0.9rem; color:#1e1b4b;
            outline:none; transition:all 0.25s; background:#f5f3ff;
        }
        .form-input:focus { border-color:#4f46e5; background:white; box-shadow:0 0 0 4px rgba(79,70,229,0.08); }
        .form-input::placeholder { color:#a5b4fc; }

        .btn-submit {
            width:100%; padding:13px;
            background:linear-gradient(135deg,#312e81,#4f46e5);
            color:white; font-family:'Inter',sans-serif;
            font-size:0.92rem; font-weight:600;
            border:none; border-radius:12px; cursor:pointer;
            transition:all 0.25s; margin-top:4px;
            box-shadow:0 4px 16px rgba(79,70,229,0.35);
            position:relative; overflow:hidden;
        }
        .btn-submit::after {
            content:''; position:absolute; top:0; left:-100%;
            width:100%; height:100%;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);
            transition:0.5s;
        }
        .btn-submit:hover::after { left:100%; }
        .btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 24px rgba(79,70,229,0.45); }

        .auth-divider { display:flex; align-items:center; gap:12px; margin:24px 0; color:#c7d2fe; font-size:0.78rem; }
        .auth-divider::before,.auth-divider::after { content:''; flex:1; height:1px; background:#e0e7ff; }

        .link-group { display:flex; flex-direction:column; gap:10px; }
        .link-btn {
            display:block; text-align:center; padding:11px 16px;
            border:1.5px solid #e0e7ff; border-radius:12px;
            font-size:0.85rem; font-weight:600; color:#4f46e5;
            text-decoration:none; transition:all 0.2s;
            background: white;
        }
        .link-btn:hover { background:#eef2ff; border-color:#a5b4fc; }

        .auth-footer { text-align:center; margin-top:12px; font-size:0.8rem; color:#8b85c0; }
        .auth-footer a { color:#4f46e5; font-weight:600; text-decoration:none; }
        .auth-footer a:hover { text-decoration:underline; }

        @media(max-width:768px){
            .auth-left{display:none;}
            .auth-right{padding:40px 24px;background:#f5f3ff;}
            .auth-form-wrap{background:white;padding:32px 24px;border-radius:24px;box-shadow:0 20px 60px rgba(79,70,229,0.12);}
        }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="left-content">
            <div class="left-logo">👑</div>
            <div class="left-badge">✦ Admin Komunitas</div>
            <div class="left-brand">BookVerse</div>
            <p class="left-tagline">Kelola komunitasmu,<br>wujudkan ruang diskusi yang seru!</p>

            <div class="left-perks">
                <div class="perk-item">
                    <div class="perk-icon">🏘️</div>
                    <div class="perk-text"><strong>Kelola Komunitas</strong>Atur anggota dan konten komunitasmu</div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon">📌</div>
                    <div class="perk-text"><strong>Panel Admin Khusus</strong>Dashboard eksklusif untuk admin komunitas</div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon">🛡️</div>
                    <div class="perk-text"><strong>Moderasi Konten</strong>Jaga diskusi tetap sehat dan berkualitas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrap">
            <h1 class="auth-title">Selamat datang kembali! 👑</h1>
            <p class="auth-subtitle">Masuk ke panel Admin Komunitas BookVerse</p>

            @if(session('info'))
                <div class="alert alert-info">💡 {{ session('info') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">❌ {{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ url('/komunitas/masuk') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input"
                           placeholder="email@komunitas.com" required autofocus
                           value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="Masukkan password" required>
                </div>
                <div class="form-group">
                    <script src="https://www.recaptcha.net/recaptcha/api.js" async defer></script>
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @error('g-recaptcha-response')
                        <span style="color:#c0392b;font-size:0.8rem;margin-top:5px;display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">Masuk ke Panel Admin →</button>
            </form>

            <div class="auth-divider">atau</div>

            <div class="link-group">
                <a href="{{ url('/komunitas/daftar') }}" class="link-btn">👑 Daftar sebagai Admin Komunitas</a>
            </div>

            <div class="auth-footer" style="margin-top:20px;">
                Pengguna biasa? <a href="{{ url('/login') }}">Masuk sebagai User</a>
                &nbsp;·&nbsp;
                <a href="{{ url('/community') }}">← Komunitas</a>
            </div>
        </div>
    </div>
</body>
</html>
