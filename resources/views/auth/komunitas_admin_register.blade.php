<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar sebagai Admin Komunitas — BookVerse</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; background: #f5f3ff; }

        .auth-left {
            width: 40%;
            background: linear-gradient(145deg, #312e81 0%, #4f46e5 55%, #818cf8 100%);
            display: flex; flex-direction: column; justify-content: center;
            align-items: center; padding: 60px 44px; position: relative; overflow: hidden;
        }
        .auth-left::before { content:''; position:absolute; top:-80px; right:-80px; width:320px; height:320px; background:rgba(255,255,255,0.06); border-radius:50%; }
        .auth-left::after  { content:''; position:absolute; bottom:-70px; left:-60px;  width:260px; height:260px; background:rgba(255,255,255,0.05); border-radius:50%; }
        .left-content { position:relative; z-index:2; text-align:center; }
        .left-logo { width:76px; height:76px; background:rgba(255,255,255,0.15); border-radius:22px; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 18px; border:1px solid rgba(255,255,255,0.2); box-shadow:0 8px 32px rgba(0,0,0,0.2); }
        .left-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); color:white; font-size:0.7rem; font-weight:700; letter-spacing:0.06em; padding:5px 14px; border-radius:20px; margin-bottom:14px; text-transform:uppercase; }
        .left-brand { font-family:'Playfair Display',serif; font-size:2rem; color:white; margin-bottom:8px; }
        .left-tagline { font-size:0.86rem; color:rgba(255,255,255,0.7); line-height:1.6; margin-bottom:36px; }

        .perks-box { background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); border-radius:16px; padding:20px; text-align:left; width:100%; max-width:270px; }
        .perks-title { font-size:0.75rem; font-weight:700; color:rgba(255,255,255,0.9); letter-spacing:0.04em; text-transform:uppercase; margin-bottom:14px; }
        .perk-item { display:flex; align-items:flex-start; gap:10px; margin-bottom:12px; }
        .perk-item:last-child { margin-bottom:0; }
        .perk-icon { font-size:1.1rem; flex-shrink:0; margin-top:1px; }
        .perk-text { font-size:0.8rem; color:rgba(255,255,255,0.85); line-height:1.4; }
        .perk-text strong { color:white; display:block; margin-bottom:1px; }

        .auth-right { flex:1; display:flex; align-items:center; justify-content:center; padding:40px 36px; background:white; overflow-y:auto; }
        .auth-form-wrap { width:100%; max-width:420px; animation:slideUp 0.5s ease; }
        @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

        .auth-title { font-family:'Playfair Display',serif; font-size:1.8rem; color:#1e1b4b; margin-bottom:6px; }
        .auth-subtitle { font-size:0.875rem; color:#8b85c0; margin-bottom:26px; }

        .info-box { background:linear-gradient(135deg,#eef2ff,#e0e7ff); border:1px solid #c7d2fe; border-radius:14px; padding:14px 18px; margin-bottom:24px; }
        .info-box-title { font-size:0.78rem; font-weight:700; color:#3730a3; margin-bottom:8px; }
        .info-box ul { list-style:none; padding:0; display:flex; flex-direction:column; gap:6px; }
        .info-box li { display:flex; align-items:flex-start; gap:8px; font-size:0.8rem; color:#4338ca; }

        .alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:12px; font-size:0.845rem; margin-bottom:18px; line-height:1.5; }
        .alert-error   { background:#fff1f1; border:1px solid #fecaca; color:#c0392b; }
        .alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }

        .form-group { margin-bottom:16px; }
        .form-label { display:block; font-size:0.8rem; font-weight:600; color:#3730a3; margin-bottom:7px; }
        .form-input { width:100%; padding:12px 16px; border:1.5px solid #e0e7ff; border-radius:12px; font-family:'Inter',sans-serif; font-size:0.9rem; color:#1e1b4b; outline:none; transition:all 0.25s; background:#f5f3ff; }
        .form-input:focus { border-color:#4f46e5; background:white; box-shadow:0 0 0 4px rgba(79,70,229,0.08); }
        .form-input::placeholder { color:#a5b4fc; }

        #strengthBar { height:4px; border-radius:2px; background:#e5e7eb; overflow:hidden; margin-top:6px; }
        #strengthFill { height:100%; width:0; border-radius:2px; transition:width .3s,background .3s; }
        #pwReqs { list-style:none; padding:0; margin:8px 0 0; display:flex; flex-wrap:wrap; gap:6px; }
        #pwReqs li { font-size:0.71rem; padding:2px 8px; border-radius:10px; background:#f3f4f6; color:#9ca3af; }

        .btn-submit { width:100%; padding:13px; background:linear-gradient(135deg,#312e81,#4f46e5); color:white; font-family:'Inter',sans-serif; font-size:0.92rem; font-weight:600; border:none; border-radius:12px; cursor:pointer; transition:all 0.25s; margin-top:4px; box-shadow:0 4px 16px rgba(79,70,229,0.35); position:relative; overflow:hidden; }
        .btn-submit::after { content:''; position:absolute; top:0; left:-100%; width:100%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent); transition:0.5s; }
        .btn-submit:hover::after { left:100%; }
        .btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 24px rgba(79,70,229,0.45); }

        .auth-divider { display:flex; align-items:center; gap:12px; margin:20px 0; color:#c7d2fe; font-size:0.78rem; }
        .auth-divider::before,.auth-divider::after { content:''; flex:1; height:1px; background:#e0e7ff; }

        .auth-footer { text-align:center; font-size:0.82rem; color:#8b85c0; }
        .auth-footer a { color:#4f46e5; font-weight:600; text-decoration:none; }
        .auth-footer a:hover { text-decoration:underline; }

        @media(max-width:768px){
            .auth-left{display:none;}
            .auth-right{padding:36px 20px;background:#f5f3ff;}
            .auth-form-wrap{background:white;padding:30px 22px;border-radius:24px;box-shadow:0 20px 60px rgba(79,70,229,0.12);}
        }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="left-content">
            <div class="left-logo">👑</div>
            <div class="left-badge">✦ Admin Komunitas</div>
            <div class="left-brand">BookVerse</div>
            <p class="left-tagline">Bangun komunitasmu,<br>jadilah pemimpin diskusi buku!</p>

            <div class="perks-box">
                <div class="perks-title">Keuntungan Admin Komunitas</div>
                <div class="perk-item">
                    <div class="perk-icon">🏘️</div>
                    <div class="perk-text"><strong>Buat Komunitas Sendiri</strong>Dengan tema buku favoritmu</div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon">👥</div>
                    <div class="perk-text"><strong>Kelola Anggota</strong>Setujui atau tolak permintaan bergabung</div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon">🛡️</div>
                    <div class="perk-text"><strong>Moderasi Konten</strong>Jaga kualitas diskusi komunitasmu</div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon">✅</div>
                    <div class="perk-text"><strong>Diaktifkan Superadmin</strong>Proses review cepat & mudah</div>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-form-wrap">
            <h1 class="auth-title">Daftar Admin Komunitas 👑</h1>
            <p class="auth-subtitle">Buat akun dan mulai perjalananmu sebagai admin</p>

            <div class="info-box">
                <div class="info-box-title">🚀 Alur Setelah Mendaftar:</div>
                <ul>
                    <li>📝 <span>Isi form pembuatan komunitas</span></li>
                    <li>⏳ <span>Tunggu persetujuan Superadmin</span></li>
                    <li>🎉 <span>Komunitas aktif & siap dikelola!</span></li>
                </ul>
            </div>

            @if(session('error'))
                <div class="alert alert-error">❌ {{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ url('/komunitas/daftar') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input"
                           placeholder="Nama pengguna unik" required autofocus
                           value="{{ old('username') }}" minlength="3" maxlength="30">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input"
                           placeholder="email@komunitas.com" required
                           value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="Min. 8 karakter" required minlength="8"
                           oninput="checkStrength(this.value)">
                    <div id="strengthBar"><div id="strengthFill"></div></div>
                    <ul id="pwReqs">
                        <li id="req-len">✗ Min. 8 karakter</li>
                        <li id="req-up">✗ Huruf kapital</li>
                        <li id="req-low">✗ Huruf kecil</li>
                        <li id="req-num">✗ Angka</li>
                    </ul>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-input" placeholder="Ulangi password" required oninput="checkMatch()">
                    <span id="matchMsg" style="font-size:0.75rem;margin-top:5px;display:block;"></span>
                </div>
                <button type="submit" class="btn-submit">🚀 Daftar & Buat Komunitas →</button>
            </form>

            <div class="auth-divider">sudah punya akun?</div>

            <div class="auth-footer">
                <a href="{{ url('/komunitas/masuk') }}">Masuk sebagai Admin Komunitas</a>
                &nbsp;·&nbsp;
                <a href="{{ url('/register') }}">Daftar sebagai User Biasa</a>
            </div>
        </div>
    </div>

<script>
function req(id,pass){var el=document.getElementById(id);if(pass){el.style.background='#dcfce7';el.style.color='#16a34a';el.textContent=el.textContent.replace('✗','✓');}else{el.style.background='#f3f4f6';el.style.color='#9ca3af';el.textContent=el.textContent.replace('✓','✗');}}
function checkStrength(v){var len=v.length>=8,up=/[A-Z]/.test(v),low=/[a-z]/.test(v),num=/[0-9]/.test(v);req('req-len',len);req('req-up',up);req('req-low',low);req('req-num',num);var score=[len,up,low,num].filter(Boolean).length;var colors=['','#ef4444','#f97316','#eab308','#22c55e'];var widths=['0%','25%','50%','75%','100%'];var fill=document.getElementById('strengthFill');fill.style.width=widths[score];fill.style.background=colors[score];}
function checkMatch(){var pw=document.getElementById('password').value;var cp=document.getElementById('password_confirmation').value;var msg=document.getElementById('matchMsg');if(!cp){msg.textContent='';return;}if(pw===cp){msg.textContent='✓ Password cocok';msg.style.color='#16a34a';}else{msg.textContent='✗ Tidak cocok';msg.style.color='#ef4444';}}
</script>
</body>
</html>
