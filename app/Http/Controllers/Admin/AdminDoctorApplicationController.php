<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDoctorApplicationController extends Controller
{
    /** GET /admin/doctor-applications — list by status */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $applications = DoctorApplication::with('user')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.doctor-applications.index', compact('applications', 'status'));
    }

    /** GET /admin/doctor-applications/{application} — application detail */
    public function show(DoctorApplication $application): View
    {
        $application->load([
            'user',
            'documents.requirement',
            'professionalTitles.professionalTitle',
        ]);

        return view('admin.doctor-applications.show', compact('application'));
    }

    /** POST /admin/doctor-applications/{application}/approve */
    public function approve(DoctorApplication $application): RedirectResponse
    {
        $application->update([
            'status'              => 'approved',
            'reviewed_at'         => now(),
            'reviewed_by_admin_id' => auth()->id(),
        ]);

        // Promote user to doctor status
        $application->user->update([
            'role'          => 'doctor',
            'doctor_status' => 'approved',
        ]);

        return redirect()->route('admin.doctor-applications.index')
            ->with('success', "Application for {$application->user->display_name} has been approved.");
    }

    /** POST /admin/doctor-applications/{application}/reject */
    public function reject(Request $request, DoctorApplication $application): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $application->update([
            'status'               => 'rejected',
            'reviewed_at'          => now(),
            'reviewed_by_admin_id' => auth()->id(),
            'admin_notes'          => $request->admin_notes,
        ]);

        $application->user->update(['doctor_status' => 'rejected']);

        return redirect()->route('admin.doctor-applications.index')
            ->with('success', 'Application rejected.');
    }
}
