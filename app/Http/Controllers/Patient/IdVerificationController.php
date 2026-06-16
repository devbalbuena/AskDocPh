<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IdVerificationController extends Controller
{
    /**
     * Show the ID verification upload form/status.
     */
    public function show(): View
    {
        return view('patient.id-verification');
    }

    /**
     * Store the uploaded ID document.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB max
        ]);

        $user = $request->user();

        if ($user->id_verification_status === 'approved') {
            return redirect()->route('patient.dashboard')->with('success', 'Your ID is already verified.');
        }

        if ($user->id_document_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->id_document_path);
        }

        $path = $request->file('id_document')->store('id_documents', 'public');

        $user->forceFill([
            'id_document_path' => $path,
            'id_verification_status' => 'pending',
            'id_verified_at' => null,
        ])->save();

        return redirect()->route('patient.id-verification.notice')
            ->with('success', 'Your ID document has been submitted and is pending review.');
    }
}
