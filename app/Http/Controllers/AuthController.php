<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect('/');
        }

        // Tampilkan pesan kontekstual jika user diarahkan dari komunitas
        if ($request->query('from') === 'community_create') {
            session()->flash('info', 'Kamu harus login atau daftar dulu untuk mengajukan komunitas. Setelah komunitas disetujui, kamu otomatis jadi Admin Komunitas! 🎉');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect('/');
        }

        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha],
        ], [
            'g-recaptcha-response.required' => 'Centang reCAPTCHA untuk melanjutkan.'
        ]);

        $user = User::where('email', $request->email)->first();

        // Google-only users (password null) cannot login with password
        if (!$user || !$user->password || !Hash::check($request->password, $user->password)) {
            return redirect()->route('login')->with('error', 'Email atau password salah.');
        }

        if ($user->status_akun === 'suspended') {
            return redirect()->route('login')->with('error', 'Akun Anda telah ditangguhkan oleh Admin karena melanggar pedoman komunitas.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        \App\Models\LogAktivitas::record($user->id, 'Login', 'User login ke sistem menggunakan email.');

        if ($user->role_id === 1) {
            return redirect('/admin/superadmin');
        }

        return redirect('/');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            return redirect('/');
        }

        // Block role escalation
        if ($request->has('role_id') && (int)$request->role_id === 1) {
            abort(403, 'Forbidden: Role escalation detected.');
        }

        $errors = [];

        $username        = trim($request->username ?? '');
        $email           = trim($request->email ?? '');
        $password        = $request->password ?? '';
        $confirmPassword = $request->confirm_password ?? '';

        if (empty($username) || strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Username harus 3-50 karakter.';
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username hanya boleh huruf, angka, dan underscore.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf kapital (A-Z).';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 huruf kecil (a-z).';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 angka (0-9).';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }
        if (User::where('email', $email)->exists()) {
            $errors[] = 'Email sudah terdaftar.';
        }
        if (User::where('username', $username)->exists()) {
            $errors[] = 'Username sudah digunakan.';
        }

        if (!empty($errors)) {
            return redirect()->route('register')->with('error', implode('<br>', $errors));
        }

        $user = User::create([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'role_id'  => 3,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        \App\Models\LogAktivitas::record($user->id, 'Register', 'User berhasil mendaftar akun baru menggunakan email.');

        // Cek jika dari ajukan komunitas
        if ($request->has('redirect_to') && $request->redirect_to === 'community_create') {
            return redirect('/community?tab=create')->with('success', 'Pendaftaran berhasil! Silakan lanjutkan membuat komunitas.');
        }

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout(Request $request)
    {
        \App\Models\LogAktivitas::record(Auth::id(), 'Logout', 'User keluar dari sistem.');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }

    // ─── GOOGLE OAUTH ─────────────────────────────────────────

    /**
     * Redirect ke halaman consent Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle callback dari Google setelah user memberi izin.
     * - Jika email sudah ada → link akun (update google_id)
     * - Jika belum ada → buat user baru dengan role_id = 3
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }

        // Cari user berdasarkan google_id atau email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if ($user->status_akun === 'suspended') {
                return redirect()->route('login')->with('error', 'Akun Anda telah ditangguhkan oleh Admin karena melanggar pedoman komunitas.');
            }

            // Akun sudah ada → update google_id & avatar jika belum terisi
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $user->avatar ?: $googleUser->getAvatar(),
            ]);

            \App\Models\LogAktivitas::record($user->id, 'Login Google', 'User login menggunakan Google OAuth.');
        } else {
            // Buat user baru dari data Google
            $baseUsername = Str::slug(
                preg_replace('/[^a-zA-Z0-9 ]/', '', $googleUser->getName() ?? 'user'),
                '_'
            );
            if (empty($baseUsername)) {
                $baseUsername = 'user';
            }

            // Pastikan username unik
            $username = $baseUsername;
            $counter  = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . '_' . $counter++;
            }

            $user = User::create([
                'username'  => $username,
                'email'     => $googleUser->getEmail(),
                'password'  => null,    // Google-only user, tidak punya password
                'role_id'   => 3,
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);

            \App\Models\LogAktivitas::record($user->id, 'Register Google', 'User mendaftar otomatis melalui Google OAuth.');
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect('/')->with('success', 'Selamat datang, ' . $user->username . '! 👋');
    }
}
