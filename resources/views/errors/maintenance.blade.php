<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Diperbaiki - BookVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { margin:0; font-family:'Inter', sans-serif; background:#f9f8fc; color:#1a1030; display:flex; align-items:center; justify-content:center; min-height:100vh; text-align:center; padding:20px; }
        h1 { font-family:'Playfair Display', serif; font-size:3rem; margin-bottom:10px; color:#e11d48; }
        p { font-size:1.1rem; color:#64748b; max-width:500px; margin:0 auto 30px; line-height:1.6; }
        .icon { font-size:5rem; margin-bottom:20px; animation: bounce 2s infinite ease-in-out; display:inline-block; }
        @keyframes bounce { 0%, 100% { transform:translateY(0); } 50% { transform:translateY(-20px); } }
        .btn-superadmin { display:inline-block; padding:10px 20px; background:#e2e8f0; color:#475569; text-decoration:none; border-radius:30px; font-size:0.85rem; font-weight:600; transition:0.3s; }
        .btn-superadmin:hover { background:#cbd5e1; }
    </style>
</head>
<body>
    <div>
        <div class="icon">🚧</div>
        <h1>Sedang Pemeliharaan</h1>
        <p>BookVerse sedang dalam masa perbaikan dan peningkatan sistem. Kami akan segera kembali dalam beberapa saat. Terima kasih atas kesabarannya!</p>
        
        <div style="margin-top:50px;">
            <a href="{{ url('/login') }}" class="btn-superadmin">Login Superadmin</a>
        </div>
    </div>
</body>
</html>
