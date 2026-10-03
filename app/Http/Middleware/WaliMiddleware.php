<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WaliMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('wali')->check()) {
            // Pengecekan Kunci Akses Login Sistem (Cut-Off & Migrasi Saldo)
            $setting = \App\Models\ApplicationSetting::first();
            if ($setting && $setting->is_login_locked) {
                Auth::guard('wali')->logout();
                $msg = $setting->getLockedMessage();
                return redirect()->route('wali.login')->with('warning', $msg);
            }

            return $next($request);
        }

        return redirect()->route('wali.login');
    }
}
