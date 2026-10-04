<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApplicationSetting;
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
            $setting = ApplicationSetting::first();
            // Jika sistem dikunci untuk migrasi, paksa auto-logout seluruh sesi wali santri
            if ($setting && $setting->is_login_locked) {
                Auth::guard('wali')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $message = $setting->getLockedMessage();
                return redirect()->route('wali.login')->with('warning', $message);
            }

            return $next($request);
        }

        return redirect()->route('wali.login');
    }
}
