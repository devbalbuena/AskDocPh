<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->two_factor_enabled && !$user->isDemo()) {
            $code = (string) random_int(100000, 999999);
            $user->forceFill([
                'two_factor_code' => $code,
                'two_factor_expires_at' => now()->addMinutes(15),
            ])->save();

            // Send standard email notification for 2FA
            $user->notify(new \App\Notifications\TwoFactorCodeNotification($code));

            $request->session()->put('2fa_user_id', $user->id);
            Auth::guard('web')->logout();
            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        return match($user->role) {
            'admin'  => redirect('/admin/dashboard'),
            'doctor' => redirect('/doctor/dashboard'),
            default  => redirect('/patient/dashboard'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
