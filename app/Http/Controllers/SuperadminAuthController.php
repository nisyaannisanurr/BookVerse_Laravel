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

        $envEmail    = env('SUPERADMIN_EMAIL');
        $envPassword = env('SUPERADMIN_PASSWORD');

        // Check against hardcoded env credentials (plain text comparison)
        if ($request->email !== $envEmail || $request->password !== $envPassword) {
            return back()
                ->with('error', 'Kredensial Superadmin tidak valid.')
                ->withInput(['email' => $request->email]);
        }

        // Find superadmin by email OR username, then ensure correct role
        $superadmin = User::where('email', $envEmail)
            ->orWhere('username', 'superadmin')
            ->first();

        if (!$superadmin) {
            // No existing user → create; 'hashed' cast will auto-hash the password
            $superadmin = User::create([
                'username' => 'superadmin_' . date('y'),
                'email'    => $envEmail,
                'password' => $envPassword,
                'role_id'  => 1,
            ]);
        } else {
            // Existing user → update email & role; reset password via DB to avoid double-hash from cast
            DB::table('users')->where('id', $superadmin->id)->update([
                'email'    => $envEmail,
                'role_id'  => 1,
                'password' => Hash::make($envPassword),
            ]);
            $superadmin->refresh();
        }

        Auth::login($superadmin);
        $request->session()->regenerate();

        return redirect('/admin/superadmin')
            ->with('success', 'Selamat datang, Superadmin!');
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
