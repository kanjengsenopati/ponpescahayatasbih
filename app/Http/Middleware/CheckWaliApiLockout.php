<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApplicationSetting;

class CheckWaliApiLockout
{
    /**
     * Handle an incoming request for mobile API.
     * Automatically log out and revoke token if system login is locked.
     */
    public function handle(Request $request, Closure $next)
    {
        $setting = ApplicationSetting::first();
        if ($setting && $setting->is_login_locked) {
            $user = $request->user();
            if ($user && method_exists($user, 'token') && $user->token()) {
                $user->token()->revoke();
            }

            return response()->json([
                'status'  => false,
                'message' => $setting->getLockedMessage(),
            ], 401);
        }

        return $next($request);
    }
}
