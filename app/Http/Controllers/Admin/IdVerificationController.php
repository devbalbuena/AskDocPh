<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IdVerificationController extends Controller
{
    /**
     * Show list of pending/rejected ID verifications
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        
        $users = User::where('role', 'patient')
            ->where('id_verification_status', $status)
            ->whereNotNull('id_document_path')
            ->latest('updated_at')
            ->paginate(20);

        return view('admin.id-verification.index', compact('users', 'status'));
    }

    /**
     * Approve or reject the ID document
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
        ]);

        if ($user->role !== 'patient' || !$user->id_document_path) {
            return back()->with('error', 'Invalid verification request.');
        }

        if ($request->action === 'approve') {
            $user->forceFill([
                'id_verification_status' => 'approved',
                'id_verified_at' => now(),
            ])->save();
            
            $msg = "Patient {$user->display_name}'s ID has been approved.";
        } else {
            $user->forceFill([
                'id_verification_status' => 'rejected',
                'id_verified_at' => null,
            ])->save();
            
            $msg = "Patient {$user->display_name}'s ID has been rejected.";
        }

        return back()->with('success', $msg);
    }
}
