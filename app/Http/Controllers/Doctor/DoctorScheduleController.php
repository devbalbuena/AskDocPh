<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    /** GET /doctor/schedule — list the doctor's schedule */
    public function index(): View
    {
        $schedules = DoctorSchedule::where('doctor_id', auth()->id())
            ->orderByRaw("FIELD(day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get();

        return view('doctor.schedule.index', compact('schedules'));
    }

    /** POST /doctor/schedule — add a new availability slot */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'day_of_week'           => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time'            => ['required', 'date_format:H:i'],
            'end_time'              => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,30,45,60'],
        ]);

        DoctorSchedule::create([
            'doctor_id'             => auth()->id(),
            'day_of_week'           => $request->day_of_week,
            'start_time'            => $request->start_time,
            'end_time'              => $request->end_time,
            'slot_duration_minutes' => $request->slot_duration_minutes,
            'is_available'          => true,
        ]);

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule slot added.');
    }

    /** PUT /doctor/schedule/{schedule} — edit a slot */
    public function update(Request $request, DoctorSchedule $schedule): RedirectResponse
    {
        abort_unless($schedule->doctor_id === auth()->id(), 403);

        $request->validate([
            'start_time'            => ['required', 'date_format:H:i'],
            'end_time'              => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,30,45,60'],
            'is_available'          => ['boolean'],
        ]);

        $schedule->update($request->only(['start_time', 'end_time', 'slot_duration_minutes', 'is_available']));

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule slot updated.');
    }

    /** DELETE /doctor/schedule/{schedule} — remove a slot */
    public function destroy(DoctorSchedule $schedule): RedirectResponse
    {
        abort_unless($schedule->doctor_id === auth()->id(), 403);

        $schedule->delete();

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule slot removed.');
    }
}
