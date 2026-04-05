<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorReferral;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferralController extends Controller
{
    /** GET /doctor/referrals — Received + Sent tabs */
    public function index(): View
    {
        $doctor = auth()->user();

        $received = DoctorReferral::where('referred_to_doctor_id', $doctor->id)
            ->with(['referringDoctor', 'patient', 'appointment'])
            ->latest()
            ->get();

        $sent = DoctorReferral::where('referring_doctor_id', $doctor->id)
            ->with(['referredToDoctor', 'patient', 'appointment'])
            ->latest()
            ->get();

        return view('doctor.referrals.index', compact('received', 'sent'));
    }

    /** POST /doctor/referrals — Create a referral from a completed appointment */
    public function store(Request $request): JsonResponse
    {
        if (auth()->user()->isDemo()) {
            return response()->json(['message' => 'Demo accounts cannot create referrals.'], 403);
        }

        $request->validate([
            'appointment_id'        => ['required', 'exists:appointments,id'],
            'referred_to_doctor_id' => ['required', 'exists:users,id'],
            'reason'                => ['required', 'string', 'max:255'],
            'message'               => ['nullable', 'string', 'max:1000'],
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        // Only allow referrals for COMPLETED appointments
        if ($appointment->status !== 'completed') {
            return response()->json(['message' => 'Referrals can only be created for completed appointments.'], 422);
        }

        // Confirm the logged-in doctor owns this appointment
        if ($appointment->doctor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Check target is a verified doctor
        $targetDoctor = User::findOrFail($request->referred_to_doctor_id);
        if ($targetDoctor->role !== 'doctor') {
            return response()->json(['message' => 'Target user is not a doctor.'], 422);
        }

        $referral = DoctorReferral::create([
            'referring_doctor_id'   => auth()->id(),
            'referred_to_doctor_id' => $request->referred_to_doctor_id,
            'patient_id'            => $appointment->patient_id,
            'appointment_id'        => $appointment->id,
            'reason'                => $request->reason,
            'message'               => $request->message,
        ]);

        // Notify target doctor
        Notification::create([
            'user_id' => $request->referred_to_doctor_id,
            'type'    => 'referral_received',
            'data'    => json_encode([
                'message'     => 'Dr. ' . auth()->user()->display_name . ' referred a patient to you.',
                'referral_id' => $referral->id,
                'patient'     => $appointment->patient->display_name ?? 'Patient',
            ]),
        ]);

        return response()->json(['success' => true, 'message' => 'Patient referred successfully.']);
    }

    /** POST /doctor/referrals/{referral}/accept */
    public function accept(DoctorReferral $referral): JsonResponse
    {
        if (auth()->user()->isDemo()) {
            return response()->json(['message' => 'Demo accounts cannot accept referrals.'], 403);
        }

        if ($referral->referred_to_doctor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $referral->update(['status' => 'accepted']);

        // Notify referring doctor
        Notification::create([
            'user_id' => $referral->referring_doctor_id,
            'type'    => 'referral_accepted',
            'data'    => json_encode([
                'message'  => 'Dr. ' . auth()->user()->display_name . ' accepted your referral.',
                'referral_id' => $referral->id,
            ]),
        ]);

        return response()->json(['success' => true]);
    }

    /** POST /doctor/referrals/{referral}/decline */
    public function decline(DoctorReferral $referral): JsonResponse
    {
        if (auth()->user()->isDemo()) {
            return response()->json(['message' => 'Demo accounts cannot decline referrals.'], 403);
        }

        if ($referral->referred_to_doctor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $referral->update(['status' => 'declined']);

        // Notify referring doctor
        Notification::create([
            'user_id' => $referral->referring_doctor_id,
            'type'    => 'referral_declined',
            'data'    => json_encode([
                'message'  => 'Dr. ' . auth()->user()->display_name . ' declined your referral.',
                'referral_id' => $referral->id,
            ]),
        ]);

        return response()->json(['success' => true]);
    }
}
