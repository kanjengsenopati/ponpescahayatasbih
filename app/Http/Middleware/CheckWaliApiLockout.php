<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApplicationSetting;

class CheckWaliApiLockout
{
    /**
     * Handle an incoming request for mobile API.
     * Enforce READ-ONLY mode for Wali Santri when system is locked for migration.
     * Wali Santri can still login and view all data (GET), but cannot make payments/mutations (POST/PUT/DELETE).
     */
    public function handle(Request $request, Closure $next)
    {
        $setting = ApplicationSetting::first();
        if ($setting && $setting->is_login_locked) {
            // Izinkan semua request baca data (GET, HEAD, OPTIONS)
            if ($request->isMethod('get') || $request->isMethod('head') || $request->isMethod('options')) {
                return $next($request);
            }

            // Izinkan request teknis perangkat
            if ($request->is('*/update-fcm-token') || $request->is('*/auth/logout') || $request->is('*/logout')) {
                return $next($request);
            }

            // Blokir semua aksi transaksi/pembayaran/mutasi finansial
            return response()->json([
                'code'    => 403,
                'success' => false,
                'message' => $setting->getLockedMessage() ?: 'Layanan pembayaran dan mutasi saldo di aplikasi lama sedang ditutup untuk proses migrasi ke aplikasi baru (Read-Only). Anda tetap dapat melihat data di aplikasi ini.',
            ], 403);
        }

        return $next($request);
    }
}
