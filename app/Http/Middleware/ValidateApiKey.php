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
        $apiKey = config('app.api_key', env('API_KEY'));

        if (empty($apiKey) || $request->header('x-api-key') != $apiKey) {
            return response()->json([
                'code'    => 401,
                'success' => false,
                'message' => 'Api key is invalid !'
            ], 401);
        }

        // Auto logout dan blokir akses jika sistem sedang dikunci untuk migrasi
        $setting = \App\Models\ApplicationSetting::first();
        if ($setting && $setting->is_login_locked) {
            $user = $request->user('api') ?: \Illuminate\Support\Facades\Auth::guard('api')->user();
            if ($user && method_exists($user, 'token') && $user->token()) {
                $user->token()->revoke();
            }

            return response()->json([
                'code'    => 401,
                'success' => false,
                'message' => $setting->getLockedMessage(),
            ], 401);
        }

        // Auto logout ketika user diblokir
        $user = $request->user('api') ?: \Illuminate\Support\Facades\Auth::guard('api')->user();
        if ($user) {
            if (!$user->is_active) {
                if (method_exists($user, 'token') && $user->token()) {
                    $user->token()->revoke();
                }

                return response()->json([
                    'code'    => 401,
                    'success' => false,
                    'message' => 'Akun Anda sedang dinonaktifkan.',
                ], 401);
            }
        }

        return $next($request);
    }
}
