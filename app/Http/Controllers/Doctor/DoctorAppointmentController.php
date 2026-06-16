<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DoctorAppointmentController extends Controller
{
    /** GET /doctor/appointments — list with optional status filter */
    public function index(Request $request): View
    {
        $doctorId = auth()->id();
        $status   = $request->query('status', 'all');

        $query = Appointment::where('doctor_id', $doctorId)
            ->with('patient')
            ->latest('appointment_date');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $appointments = $query->paginate(15);

        return view('doctor.appointments.index', compact('appointments', 'status'));
    }

    /** GET /doctor/appointments/{appointment} — appointment detail */
    public function show(Appointment $appointment): View
    {
        abort_unless($appointment->doctor_id === auth()->id(), 403);

        $appointment->load(['patient', 'notes.doctor']);

        // Pass verified doctors for the referral modal (exclude self)
        $verifiedDoctors = User::where('role', 'doctor')
            ->where('id', '!=', auth()->id())
            ->where('doctor_status', 'approved')
            ->orderBy('lname')
            ->get(['id', 'fname', 'lname', 'bio']);

        return view('doctor.appointments.show', compact('appointment', 'verifiedDoctors'));
    }

    /** POST /doctor/appointments/{appointment}/confirm */
    public function confirm(Appointment $appointment, Request $request): JsonResponse|RedirectResponse
    {
        abort_unless($appointment->doctor_id === auth()->id(), 403);
        abort_unless($appointment->status === 'pending', 422);

        $appointment->update(['status' => 'confirmed']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'confirmed', 'message' => 'Appointment confirmed.']);
        }

        return redirect()->route('doctor.appointments.show', $appointment)
            ->with('success', 'Appointment confirmed.');
    }

    /** POST /doctor/appointments/{appointment}/complete */
    public function complete(Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->doctor_id === auth()->id(), 403);
        abort_unless($appointment->status === 'confirmed', 422);

        $appointment->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('doctor.appointments.show', $appointment)
            ->with('success', 'Appointment marked as completed.');
    }

    /** POST /doctor/appointments/{appointment}/cancel */
    public function cancel(Appointment $appointment, Request $request): JsonResponse|RedirectResponse
    {
        abort_unless($appointment->doctor_id === auth()->id(), 403);
        abort_unless(in_array($appointment->status, ['pending', 'confirmed']), 422);

        $appointment->update([
            'status'       => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'cancelled', 'message' => 'Appointment cancelled.']);
        }

        return redirect()->route('doctor.appointments.index')
            ->with('success', 'Appointment cancelled.');
    }
}

