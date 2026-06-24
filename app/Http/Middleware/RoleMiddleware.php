<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRoleId = auth()->user()->role_id;
        $requiredRoleId = (int) $role;

        // Superadmin (role 1) selalu lolos — bisa akses semua panel (hierarki role)
        if ($userRoleId === 1) {
            return $next($request);
        }

        if ($userRoleId !== $requiredRoleId) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
