<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\View\View;

class DoctorDashboardController extends Controller
{
    /** GET /doctor/dashboard */
    public function index(): View
    {
        $doctorId = auth()->id();

        $todayAppointments = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('patient')
            ->orderBy('start_time')
            ->get();

        $pendingCount = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'pending')
            ->count();

        $totalCount = Appointment::where('doctor_id', $doctorId)->count();

        $completedCount = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->count();

        return view('doctor.dashboard', compact(
            'todayAppointments',
            'pendingCount',
            'totalCount',
            'completedCount',
        ));
    }
}
