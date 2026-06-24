<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA WA — BookVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #f9fafb 100%);
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .superadmin-card {
            background: #fff;
            border-radius: 28px;
            padding: 48px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.08);
            text-align: center;
        }
        .icon-wrap {
            width: 80px; height: 80px;
            background: #eff6ff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #2563eb;
            margin: 0 auto 24px;
            box-shadow: 0 0 0 10px rgba(37, 99, 235, 0.05);
        }
        h1 { font-weight: 800; font-size: 1.8rem; margin: 0 0 12px; color: #111827; }
        .subtitle { color: #6b7280; font-size: 0.95rem; margin-bottom: 36px; line-height: 1.6; }
        
        .otp-container {
            display: flex; justify-content: center; gap: 12px; margin-bottom: 28px;
        }
        .otp-box {
            width: 54px; height: 64px;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            font-size: 1.8rem; font-weight: 700; font-family: monospace;
            text-align: center; color: #1f2937;
            background: #f9fafb;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .otp-box:focus {
            border-color: #2563eb; background: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
            outline: none; transform: translateY(-2px);
        }
        
        .timer-wrap {
            background: #fef2f2;
            color: #ef4444;
            padding: 10px 16px;
            border-radius: 99px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 32px;
        }
        .timer-icon { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

        .btn-login {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white; border: none; border-radius: 16px;
            font-weight: 700; font-size: 1.05rem; cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
        .btn-login:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(37, 99, 235, 0.4); }
        .btn-login:active { transform: translateY(0); }
        
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #ef4444; padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.9rem; text-align: left; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #22c55e; padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.9rem; text-align: left; }
    </style>
</head>
<body>

    <div class="superadmin-card">
        <div class="icon-wrap">🛡️</div>
        <h1>Verifikasi 2FA WA</h1>
        <p class="subtitle">Kami telah mengirimkan 6 digit kode OTP ke WhatsApp Anda. Kode ini berlaku selama 5 menit.</p>

        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="timer-wrap" id="timerContainer">
            <span class="timer-icon">⏳</span> Waktu tersisa: <span id="time">05:00</span>
        </div>

        <form method="POST" action="{{ url('/superadmin/verify-otp') }}" id="otpForm">
            @csrf
            <input type="hidden" name="otp" id="realOtpInput">
            
            <div class="otp-container">
                <input type="text" class="otp-box" maxlength="1" autofocus autocomplete="off">
                <input type="text" class="otp-box" maxlength="1" autocomplete="off">
                <input type="text" class="otp-box" maxlength="1" autocomplete="off">
                <input type="text" class="otp-box" maxlength="1" autocomplete="off">
                <input type="text" class="otp-box" maxlength="1" autocomplete="off">
                <input type="text" class="otp-box" maxlength="1" autocomplete="off">
            </div>

            <button type="submit" class="btn-login" id="submitBtn">Verifikasi Sekarang</button>
        </form>
    </div>

    <script>
        // Countdown Timer
        let remainingSeconds = {{ isset($remainingSeconds) ? $remainingSeconds : 300 }};
        const timeDisplay = document.getElementById('time');
        const submitBtn = document.getElementById('submitBtn');
        const timerContainer = document.getElementById('timerContainer');
        const otpBoxes = document.querySelectorAll('.otp-box');

        function updateTimer() {
            if (remainingSeconds <= 0) {
                timeDisplay.textContent = "00:00";
                timerContainer.style.color = '#9ca3af';
                timerContainer.style.background = '#f3f4f6';
                submitBtn.disabled = true;
                submitBtn.style.background = '#9ca3af';
                submitBtn.textContent = "Kode Kedaluwarsa";
                otpBoxes.forEach(box => box.disabled = true);
                return;
            }
            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = remainingSeconds % 60;
            timeDisplay.textContent = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            remainingSeconds--;
            setTimeout(updateTimer, 1000);
        }
        updateTimer();

        // OTP Auto-advance
        const form = document.getElementById('otpForm');
        const hiddenInput = document.getElementById('realOtpInput');

        otpBoxes.forEach((box, index) => {
            box.addEventListener('input', (e) => {
                // Allow only numbers
                box.value = box.value.replace(/[^0-9]/g, '');
                if (box.value !== '' && index < otpBoxes.length - 1) {
                    otpBoxes[index + 1].focus();
                }
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && box.value === '' && index > 0) {
                    otpBoxes[index - 1].focus();
                }
            });
            
            // Allow pasting full 6 digits
            box.addEventListener('paste', (e) => {
                e.preventDefault();
                let pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').substring(0, 6);
                for(let i=0; i<pasteData.length; i++){
                    otpBoxes[i].value = pasteData[i];
                }
                if(pasteData.length > 0) {
                    otpBoxes[Math.min(pasteData.length - 1, 5)].focus();
                }
            });
        });

        form.addEventListener('submit', (e) => {
            let otpValue = '';
            otpBoxes.forEach(box => otpValue += box.value);
            hiddenInput.value = otpValue;
        });
    </script>
</body>
</html>
