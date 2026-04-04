<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isDemo() || $user->email_verified_at) {
            return match($user->role) {
                'admin'  => redirect('/admin/dashboard'),
                'doctor' => redirect('/doctor/dashboard'),
                default  => redirect('/patient/dashboard'),
            };
        }

        return view('auth.verify-email');
    }

    /**
     * Resend the email verification link.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isDemo()) {
            return back()->with('success', 'Demo accounts are pre-verified.');
        }

        if ($user->email_verified_at) {
            return match($user->role) {
                'admin'  => redirect('/admin/dashboard'),
                'doctor' => redirect('/doctor/dashboard'),
                default  => redirect('/patient/dashboard'),
            };
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'A new verification link has been sent to the email address you provided during registration.');
    }

    /**
     * Verify the user's email address.
     */
    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        if (!$request->hasValidSignature()) {
            return redirect('/email/verify')->withErrors(['email' => 'Invalid or expired verification link.']);
        }

        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->email))) {
            return redirect('/email/verify')->withErrors(['email' => 'Invalid or expired verification link.']);
        }

        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }

        $redirectPath = match($user->role) {
            'admin'  => '/admin/dashboard',
            'doctor' => '/doctor/dashboard',
            default  => '/patient/dashboard',
        };

        return redirect($redirectPath)->with('success', 'Email verified successfully!');
    }
}
