<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\AuthRequest;

class AuthController extends Controller
{
    public function index()
    {
        return view('admins.auth.login');
    }

    public function authenticate(AuthRequest $request)
    {
        if (Auth::guard('web')->attempt($request->validated(), $request->remember)) {
            $user = Auth::guard('web')->user();

            if (!$user->is_active) {
                Auth::guard('web')->logout();
                return back()->with(['warning' => 'Maaf akun tidak aktif / diblokir, silakan hubungi administrator !!'])->withInput($request->only('email'));
            }

            // Pengecekan Kunci Akses Login Sistem (Cut-Off & Migrasi Saldo)
            $setting = \App\Models\ApplicationSetting::first();
            if ($setting && $setting->is_login_locked) {
                $allowedRoles = $setting->getAllowedRoles();
                $hasAllowedRole = false;
                foreach ($allowedRoles as $roleName) {
                    if ($user->hasRole($roleName)) {
                        $hasAllowedRole = true;
                        break;
                    }
                }

                if (!$hasAllowedRole) {
                    Auth::guard('web')->logout();
                    $msg = $setting->getLockedMessage();
                    return back()->with(['warning' => $msg])->withInput($request->only('email'));
                }
            }

            $user->update([
                'last_login_at' => now(),
            ]);
            return redirect()->intended('dashboard');
        } else {
            return back()->with(['warning' => 'Maaf email atau password tidak sesuai'])->withInput($request->only('email'));
        }
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return  redirect('/');
    }
}
