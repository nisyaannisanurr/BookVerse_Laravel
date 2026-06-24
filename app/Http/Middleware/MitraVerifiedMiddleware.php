<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Pastikan Mitra sudah diverifikasi (status_verifikasi = 'approved') 
 * sebelum bisa membuat kampanye atau mengakses fitur penuh Mitra.
 * Gunakan setelah MitraMiddleware.
 */
class MitraVerifiedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user->isMitraVerified()) {
            return redirect('/donasi/dashboard')
                ->with('error', 'Fitur ini hanya tersedia setelah akun Mitra Anda diverifikasi oleh admin. Silakan tunggu persetujuan.');
        }

        return $next($request);
    }
}
