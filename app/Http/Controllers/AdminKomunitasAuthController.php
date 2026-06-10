<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminKomunitasAuthController extends Controller
{
    // ─── Tampilkan halaman login Admin Komunitas ───────────
    public function showLogin()
    {
        // Jika sudah login sebagai Admin Komunitas
        if (Auth::check() && Auth::user()->role_id === 2) {
            return redirect('/admin/komunitas');
        }
        // User biasa yang sudah login tetap bisa lihat halaman ini
        // (mereka akan logout otomatis saat login sebagai admin komunitas)
        return view('auth.komunitas_admin_login');
    }

    // ─── Proses login ─────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha],
        ], [
            'g-recaptcha-response.required' => 'Centang reCAPTCHA untuk melanjutkan.'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role_id !== 2) {
            return back()->with('error', 'Akun tidak ditemukan atau bukan Admin Komunitas.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah.');
        }

        Auth::login($user);
        return redirect('/admin/komunitas')->with('success', 'Selamat datang, Admin Komunitas!');
    }

    // ─── Tampilkan halaman daftar Admin Komunitas ─────────
    public function showRegister()
    {
        // Jika sudah login sebagai Admin Komunitas
        if (Auth::check() && Auth::user()->role_id === 2) {
            return redirect('/admin/komunitas');
        }
        // Jika sudah login sebagai Superadmin, tolak
        if (Auth::check() && Auth::user()->role_id === 1) {
            return redirect('/community');
        }
        // User biasa (role_id=3) yang sudah login boleh daftar jadi admin komunitas
        // Mereka akan di-logout dari sesi lama saat akun baru dibuat
        return view('auth.komunitas_admin_register');
    }

    // ─── Proses registrasi — langsung role Admin Komunitas ───
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|min:3|max:30',
            'email'    => 'required|email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',      // min 1 huruf kapital
                'regex:/[a-z]/',      // min 1 huruf kecil
                'regex:/[0-9]/',      // min 1 angka
            ],
        ], [
            'password.min'     => 'Password minimal 8 karakter.',
            'password.regex'   => 'Password harus kombinasi huruf kapital, huruf kecil, dan angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Cek duplikat
        if (User::where('email', $request->email)->exists()) {
            return back()->with('error', 'Email sudah terdaftar.')->withInput();
        }
        if (User::where('username', $request->username)->exists()) {
            return back()->with('error', 'Username sudah digunakan.')->withInput();
        }

        // Logout dari sesi lama jika ada (misal login sebagai user biasa)
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Buat akun langsung dengan role Admin Komunitas (role_id = 2)
        $user = User::create([
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => 2, // Admin Komunitas
        ]);

        Auth::login($user);

        return redirect('/community/create')
            ->with('success', 'Akun Admin Komunitas berhasil dibuat! Sekarang buat komunitasmu. 🎉');
    }

    // ─── Logout ───────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/komunitas/masuk')->with('success', 'Berhasil keluar.');
    }
}
