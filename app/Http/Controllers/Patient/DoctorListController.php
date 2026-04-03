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
        $query = User::where('role', 'doctor')
            ->where('doctor_status', 'approved')
            ->with(['doctorApplications.professionalTitles.professionalTitle']);

        $allDoctors = $query->get();

        // Dynamically build list of unique specializations
        $specializations = [];
        foreach ($allDoctors as $d) {
            $bio = is_string($d->bio) ? json_decode($d->bio, true) : null;
            if (is_array($bio) && !empty($bio['specialization'])) {
                $spec = trim($bio['specialization']);
                if (!in_array($spec, $specializations)) {
                    $specializations[] = $spec;
                }
            }
        }
        sort($specializations);

        $doctorsCol = $allDoctors->filter(function($d) {
            $req = request();

            // Filter Specialization
            if ($req->specialization) {
                $bio = is_string($d->bio) ? json_decode($d->bio, true) : null;
                $docSpec = is_array($bio) && isset($bio['specialization']) ? $bio['specialization'] : '';
                if (!str_contains(strtolower($docSpec), strtolower($req->specialization))) {
                    return false;
                }
            }

            // Filter Availability
            if ($req->available_week) {
                $hasSchedule = DoctorSchedule::where('doctor_id', $d->id)->where('is_available', true)->exists();
                if (!$hasSchedule) return false;
            }

            return true;
        })->map(function($doctor) {
            $next = DoctorSchedule::where('doctor_id', $doctor->id)->where('is_available', true)->first();
            $doctor->next_available = $next ? ucfirst($next->day_of_week) : null;
            return $doctor;
        });

        // Manually paginate the collection
        $perPage = 12;
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $doctors = new \Illuminate\Pagination\LengthAwarePaginator(
            $doctorsCol->forPage($page, $perPage),
            $doctorsCol->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('patient.doctors.index', compact('doctors', 'specializations'));
    }

    /** GET /patient/doctors/{doctor}/schedule — show available slots */
    public function schedule(User $doctor): View
    {
        abort_unless($doctor->role === 'doctor' && $doctor->doctor_status === 'approved', 404);

        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('is_available', true)
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week')
            ->map(function ($slots) {
                return $slots->map(function ($slot) {
                    return [
                        'id' => $slot->id,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'formatted' => \Carbon\Carbon::parse($slot->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($slot->end_time)->format('g:i A')
                    ];
                });
            });

        $doctor->load(['doctorApplications.professionalTitles.professionalTitle']);

        return view('patient.doctors.schedule', compact('doctor', 'schedules'));
    }
}
