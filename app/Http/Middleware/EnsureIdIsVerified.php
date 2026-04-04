<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow demo accounts, admins, and doctors to bypass patient ID checks
        if (!$user || $user->isDemo() || $user->role !== 'patient') {
            return $next($request);
        }

        if (!$user->isVerifiedPatient()) {
            return redirect()->route('patient.id-verification.notice')
                ->with('error', 'You must verify your identity to access this page.');
        }

        return $next($request);
    }
}
