<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $maintenance = \App\Models\SystemSetting::getSetting('maintenance_mode', 'false') === 'true';
            
            // Allow if not maintenance, or if user is superadmin
            if ($maintenance && (!auth()->check() || !auth()->user()->isSuperadmin())) {
                // Allow login and logout routes so admin can login
                if (!$request->is('login') && !$request->is('logout') && !$request->is('admin*') && !$request->is('login/*')) {
                    return response()->view('errors.maintenance');
                }
            }
        } catch (\Exception $e) {
            // Ignore if DB is not ready
        }

        return $next($request);
    }
}
