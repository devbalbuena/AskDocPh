<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA challenge view.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (!$user || $user->two_factor_expires_at < now()) {
            return redirect()->route('login')->withErrors(['email' => '2FA code has expired. Please log in again.']);
        }

        return view('auth.two-factor', compact('user'));
    }

    /**
     * Verify the 2FA code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        if ($user->two_factor_expires_at < now() || $user->two_factor_code !== $request->code) {
            return back()->withErrors(['code' => 'The provided two-factor authentication code was invalid or has expired.']);
        }

        Auth::login($user);

        // Clear 2FA session details
        $request->session()->forget('2fa_user_id');
        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        $request->session()->regenerate();

        return match($user->role) {
            'admin'  => redirect('/admin/dashboard'),
            'doctor' => redirect('/doctor/dashboard'),
            default  => redirect('/patient/dashboard'),
        };
    }
}
