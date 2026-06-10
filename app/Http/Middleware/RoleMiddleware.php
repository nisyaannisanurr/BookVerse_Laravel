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

        $roleId = (int) $role;

        if (auth()->user()->role_id !== $roleId) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
