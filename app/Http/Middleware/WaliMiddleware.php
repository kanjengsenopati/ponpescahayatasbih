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
            // Jika sistem dikunci untuk migrasi, jadikan mode Read-Only (blokir mutasi / POST)
            if ($setting && $setting->is_login_locked) {
                if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
                    if (!$request->routeIs('wali.logout')) {
                        return redirect()->back()->with('error', 'Silahkan menggunakan aplikasi baru. Layanan transaksi dan pembayaran di aplikasi lama telah ditutup.');
                    }
                }
            }

            return $next($request);
        }

        return redirect()->route('wali.login');
    }
}
