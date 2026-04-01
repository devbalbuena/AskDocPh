<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentNoteController extends Controller
{
    /** POST /doctor/appointments/{appointment}/notes */
    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->doctor_id === auth()->id(), 403);

        $request->validate([
            'notes'                 => ['required', 'string', 'max:5000'],
            'diagnosis'             => ['nullable', 'string', 'max:2000'],
            'recommendations'       => ['nullable', 'string', 'max:2000'],
            'is_visible_to_patient' => ['boolean'],
        ]);

        AppointmentNote::create([
            'appointment_id'        => $appointment->id,
            'doctor_id'             => auth()->id(),
            'notes'                 => $request->notes,
            'diagnosis'             => $request->diagnosis,
            'recommendations'       => $request->recommendations,
            'is_visible_to_patient' => $request->boolean('is_visible_to_patient'),
        ]);

        return redirect()->route('doctor.appointments.show', $appointment)
            ->with('success', 'Note added.');
    }

    /** PUT /doctor/appointments/{appointment}/notes/{note} */
    public function update(Request $request, Appointment $appointment, AppointmentNote $note): RedirectResponse
    {
        abort_unless($note->doctor_id === auth()->id(), 403);
        abort_unless($note->appointment_id === $appointment->id, 403);

        $request->validate([
            'notes'                 => ['required', 'string', 'max:5000'],
            'diagnosis'             => ['nullable', 'string', 'max:2000'],
            'recommendations'       => ['nullable', 'string', 'max:2000'],
            'is_visible_to_patient' => ['boolean'],
        ]);

        $note->update($request->only(['notes', 'diagnosis', 'recommendations', 'is_visible_to_patient']));

        return redirect()->route('doctor.appointments.show', $appointment)
            ->with('success', 'Note updated.');
    }

    /** GET /doctor/appointments/{appointment}/notes/{note} */
    public function show(Appointment $appointment, AppointmentNote $note): View
    {
        // Patients may only view if is_visible_to_patient is true
        $user = auth()->user();

        if ($user->role === 'patient') {
            abort_unless($appointment->patient_id === $user->id, 403);
            abort_unless($note->is_visible_to_patient, 403);
        } else {
            abort_unless($appointment->doctor_id === $user->id, 403);
        }

        return view('doctor.appointments.note', compact('appointment', 'note'));
    }
}
