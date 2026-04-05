<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

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

        $totalPatients = Appointment::where('doctor_id', $doctorId)
            ->distinct('patient_id')
            ->count('patient_id');

        $weekAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [today(), today()->addDays(7)])
            ->count();

        $nonCancelledCount = Appointment::where('doctor_id', $doctorId)
            ->where('status', '!=', 'cancelled')
            ->count();
            
        $completionRate = $nonCancelledCount > 0 ? round(($completedCount / $nonCancelledCount) * 100) : 0;

        $nextAppointment = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'confirmed')
            ->where(function($query) {
                $query->where('appointment_date', '>', today())
                      ->orWhere(function($q) {
                          $q->where('appointment_date', today())
                            ->where('start_time', '>=', now()->format('H:i'));
                      });
            })
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->with('patient')
            ->first();

        return view('doctor.dashboard', compact(
            'todayAppointments',
            'pendingCount',
            'totalCount',
            'completedCount',
            'totalPatients',
            'weekAppointments',
            'completionRate',
            'nextAppointment',
        ));
    }

    /** POST /doctor/status/update */
    public function updateStatus(Request $request): JsonResponse
    {
        if (auth()->check() && auth()->user()->isDemo()) {
            // Demo passthrough
        }
        
        $request->validate([
            'status' => 'required|in:online,away,offline'
        ]);
        
        $user = auth()->user();
        $user->online_status = $request->status;
        $user->save();
        
        return response()->json(['success' => true, 'status' => $user->online_status]);
    }
}
