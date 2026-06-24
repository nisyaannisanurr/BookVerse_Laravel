<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperadminAuthController extends Controller
{
    /**
     * Show the dedicated superadmin login page.
     */
    public function showLogin()
    {
        // Already logged in as superadmin → go to dashboard
        if (Auth::check() && Auth::user()->role_id === 1) {
            return redirect('/admin/superadmin');
        }

        // Already logged in as someone else → logout first
        if (Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return view('auth.superadmin_login');
    }

    /**
     * Handle superadmin login with hardcoded credentials from .env
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha],
        ], [
            'g-recaptcha-response.required' => 'Centang reCAPTCHA untuk melanjutkan.'
        ]);

        // Cari user di database dengan role superadmin (role_id = 1)
        $superadmin = User::where('email', $request->email)->where('role_id', 1)->first();

        // Verifikasi keberadaan user dan kecocokan password
        if (!$superadmin || !Hash::check($request->password, $superadmin->password)) {
            return back()
                ->with('error', 'Kredensial Superadmin tidak valid.')
                ->withInput(['email' => $request->email]);
        }

        // Jika akun ditangguhkan
        if ($superadmin->status_akun === 'suspended') {
            return back()->with('error', 'Akun Anda telah ditangguhkan.');
        }

        // Generate OTP
        $otpCode = sprintf("%06d", mt_rand(1, 999999));
        
        DB::table('users')->where('id', $superadmin->id)->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Kirim OTP via WhatsApp
        $waService = new \App\Services\WhatsAppService();
        $targetNumber = $superadmin->no_wa ?? '083843509789'; // Menggunakan nomor Anggun sebagai penerima OTP utama
        $waService->sendOTP($targetNumber, $otpCode);

        // Store user ID in session for the OTP step
        $request->session()->put('otp_user_id', $superadmin->id);

        return redirect('/superadmin/verify-otp')->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda.');
    }

    /**
     * Show the OTP verification page
     */
    public function showVerifyOtp(Request $request)
    {
        if (!$request->session()->has('otp_user_id')) {
            return redirect()->route('superadmin.login');
        }

        $userId = $request->session()->get('otp_user_id');
        $user = DB::table('users')->where('id', $userId)->first();
        
        $expiresAt = \Carbon\Carbon::parse($user->otp_expires_at);
        $remainingSeconds = max(0, $expiresAt->diffInSeconds(now(), false) * -1);

        return view('auth.superadmin_verify_otp', [
            'remainingSeconds' => $remainingSeconds
        ]);
    }

    /**
     * Verify the submitted OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = $request->session()->get('otp_user_id');
        if (!$userId) {
            return redirect('/superadmin/login')->with('error', 'Sesi login telah berakhir.');
        }

        $superadmin = User::find($userId);
        if (!$superadmin) {
            return redirect('/superadmin/login');
        }

        if ($superadmin->otp_code !== $request->otp) {
            return back()->with('error', 'Kode OTP tidak valid.');
        }

        if (now()->greaterThan($superadmin->otp_expires_at)) {
            return back()->with('error', 'Kode OTP telah kedaluwarsa. Silakan login kembali.');
        }

        // Clear OTP and login
        DB::table('users')->where('id', $superadmin->id)->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        $request->session()->forget('otp_user_id');

        Auth::login($superadmin);
        $request->session()->regenerate();

        if ($superadmin->role_id === 4) {
            return redirect('/donasi/dashboard')
                ->with('success', 'Selamat datang, Mitra! Verifikasi 2FA WhatsApp berhasil.');
        }

        return redirect('/admin/superadmin')
            ->with('success', 'Selamat datang, Superadmin! Verifikasi 2FA WhatsApp berhasil.');
    }
    /**
     * Superadmin logout → redirect to superadmin login
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/superadmin/login')
            ->with('success', 'Anda telah keluar dari panel Superadmin.');
    }
}
