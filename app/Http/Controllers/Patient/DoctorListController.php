<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\View\View;

class DoctorListController extends Controller
{
    /** GET /patient/doctors — list all approved doctors */
    public function index(): View
    {
        $doctors = User::where('role', 'doctor')
            ->where('doctor_status', 'approved')
            ->with(['doctorApplications.professionalTitles.professionalTitle'])
            ->paginate(12);

        return view('patient.doctors.index', compact('doctors'));
    }

    /** GET /patient/doctors/{doctor}/schedule — show available slots */
    public function schedule(User $doctor): View
    {
        abort_unless($doctor->role === 'doctor' && $doctor->doctor_status === 'approved', 404);

        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('is_available', true)
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get()
            ->groupBy('day_of_week');

        $doctor->load(['doctorApplications.professionalTitles.professionalTitle']);

        return view('patient.doctors.schedule', compact('doctor', 'schedules'));
    }
}
