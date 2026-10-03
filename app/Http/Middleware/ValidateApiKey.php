<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (empty(env('API_KEY')) || $request->header('x-api-key') != env('API_KEY')) {
            return response()->json([
                'code'  => 401,
                'success' => false,
                'message' => 'Api key is invalid !'
            ]);
        }

        // Pengecekan Kunci Akses Sistem (Cut-Off & Migrasi Saldo) untuk mobile apps
        $setting = \App\Models\ApplicationSetting::first();
        if ($setting && $setting->is_login_locked) {
            // Biarkan endpoint login agar ditangani AuthController dengan format response yang tepat
            if (!$request->is('api/v1/auth/login')) {
                return response()->json([
                    'code' => 403,
                    'success' => false,
                    'message' => $setting->getLockedMessage(),
                ], 403);
            }
        }

        //auto logout ketika user di blokir
        if ($request->user()) {
            if (!$request->user()->is_active) {
                $request->user()->token()->revoke();
            }
        }

        return $next($request);
    }
}
