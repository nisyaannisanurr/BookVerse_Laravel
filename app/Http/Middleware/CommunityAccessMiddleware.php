<?php

namespace App\Http\Middleware;

use App\Models\AnggotaKomunitas;
use App\Models\Komunitas;
use Closure;
use Illuminate\Http\Request;

class CommunityAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $komunitasId = $request->route('komunitas_id') ?? $request->route('id');

        if (!$komunitasId) {
            abort(403);
        }

        $user = auth()->user();

        // Superadmin always has access
        if ($user->isSuperadmin()) {
            return $next($request);
        }

        // Community creator always has access
        $komunitas = Komunitas::find($komunitasId);
        if ($komunitas && $komunitas->creator_id == $user->id) {
            return $next($request);
        }

        // Check approved membership
        $membership = AnggotaKomunitas::where('user_id', $user->id)
            ->where('komunitas_id', $komunitasId)
            ->where('status', 'approved')
            ->first();

        if (!$membership || !$komunitas || $komunitas->status !== 'aktif') {
            abort(403, 'Akses ditolak. Anda bukan anggota komunitas ini.');
        }

        return $next($request);
    }
}
