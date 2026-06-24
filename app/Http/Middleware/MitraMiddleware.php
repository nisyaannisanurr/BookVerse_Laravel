<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Pastikan user yang mengakses adalah Mitra (role_id = 4).
 * Jika bukan, redirect ke login mitra.
 */
class MitraMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('mitra.login')
                ->with('error', 'Silakan login sebagai Mitra terlebih dahulu.');
        }

        if (auth()->user()->role_id !== 4) {
            auth()->logout();
            return redirect()->route('mitra.login')
                ->with('error', 'Akses ini khusus untuk akun Mitra.');
        }

        return $next($request);
    }
}
