<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrisisReport;
use App\Models\DoctorApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /** GET /admin/dashboard — aggregate stats for Chart.js */
    public function index(): View
    {
        $totalPatients = User::where('role', 'patient')->count();
        $totalDoctors  = User::where('role', 'doctor')
            ->where('doctor_status', 'approved')
            ->count();
        $pendingApplications = DoctorApplication::where('status', 'pending')->count();
        $crisisReports       = CrisisReport::where('status', 'pending')->count();

        // Registrations for the last 7 days (for Chart.js bar chart)
        $registrationsLastWeek = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $registrationsLastWeek[] = [
                'date'  => $day->format('M d'),
                'count' => User::whereDate('created_at', $day->toDateString())->count(),
            ];
        }

        // Recent crisis reports
        $recentCrisisReports = CrisisReport::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Recent doctor applications
        $recentApplications = DoctorApplication::with('user')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPatients',
            'totalDoctors',
            'pendingApplications',
            'crisisReports',
            'registrationsLastWeek',
            'recentCrisisReports',
            'recentApplications',
        ));
    }
}
