<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /** GET /patient/appointments — list patient's appointments grouped */
    public function index(): View
    {
        $userId = auth()->id();

        $upcoming = Appointment::where('patient_id', $userId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', today())
            ->with('doctor')
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        $past = Appointment::where('patient_id', $userId)
            ->whereIn('status', ['completed', 'cancelled'])
            ->orWhere(function ($q) use ($userId) {
                $q->where('patient_id', $userId)
                  ->where('appointment_date', '<', today());
            })
            ->with('doctor')
            ->orderByDesc('appointment_date')
            ->limit(20)
            ->get();

        return view('patient.appointments.index', compact('upcoming', 'past'));
    }

    /** POST /patient/appointments — book a new appointment */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i,H:i:s',
            'end_time' => 'nullable|date_format:H:i,H:i:s',
            'reason' => 'required|string|min:10|max:500',
            'appointment_date' => 'required|date|after:today',
            'doctor_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:doctor_schedules,id',
            'type' => 'required|in:online,in_person',
        ]);

        $schedule = DoctorSchedule::find($request->schedule_id);
        if (!$schedule) {
            return back()->withErrors(['schedule_id' => 'Invalid schedule.']);
        }

        // Prevent double-booking same slot
        $clash = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('start_time', $request->start_time)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($clash) {
            return back()->withErrors(['appointment_date' => 'This time slot is already booked. Please choose another.']);
        }

        $endTime = \Carbon\Carbon::parse($request->start_time)
            ->addMinutes($schedule->slot_duration_minutes)
            ->format('H:i:s');

        Appointment::create([
            'patient_id'       => auth()->id(),
            'doctor_id'        => $request->doctor_id,
            'schedule_id'      => $request->schedule_id,
            'appointment_date' => $request->appointment_date,
            'start_time'       => $request->start_time,
            'end_time'         => $endTime,
            'type'             => $request->type,
            'reason'           => $request->reason,
            'status'           => 'pending',
        ]);

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Appointment booked successfully!');
    }

    /** GET /patient/appointments/{appointment} — appointment detail */
    public function show(Appointment $appointment): View
    {
        abort_unless($appointment->patient_id === auth()->id(), 403);

        $appointment->load(['doctor', 'notes' => function ($q) {
            $q->where('is_visible_to_patient', true);
        }]);

        return view('patient.appointments.show', compact('appointment'));
    }

    /** POST /patient/appointments/{appointment}/cancel */
    public function cancel(Appointment $appointment, Request $request): RedirectResponse
    {
        abort_unless($appointment->patient_id === auth()->id(), 403);
        abort_unless(in_array($appointment->status, ['pending', 'confirmed']), 422);

        $appointment->update([
            'status'       => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Appointment cancelled.');
    }
}
