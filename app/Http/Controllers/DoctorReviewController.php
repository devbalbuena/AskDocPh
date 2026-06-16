<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorReviewController extends Controller
{
    /** POST /patient/appointments/{appointment}/review — submit review after completed appointment */
    public function store(Request $request, Appointment $appointment = null): JsonResponse|RedirectResponse
    {
        // Works also via generic /doctor-reviews route, but prefers appointment-based
        if ($appointment === null) {
            // Called from the generic route
            $request->validate([
                'appointment_id' => ['required', 'exists:appointments,id'],
                'rating'         => ['required', 'integer', 'min:1', 'max:5'],
                'review_text'    => ['nullable', 'string', 'max:2000'],
            ]);
            $appointment = Appointment::findOrFail($request->appointment_id);
        } else {
            $request->validate([
                'rating'      => ['required', 'integer', 'min:1', 'max:5'],
                'review_text' => ['nullable', 'string', 'max:2000'],
            ]);
        }

        // Must be the patient for this appointment
        abort_unless($appointment->patient_id === auth()->id(), 403);

        // Must be completed
        abort_unless($appointment->status === 'completed', 422);

        // One review per appointment
        $existing = DoctorReview::where('appointment_id', $appointment->id)
            ->where('patient_id', auth()->id())
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already reviewed this appointment.'], 422);
        }

        $review = DoctorReview::create([
            'appointment_id' => $appointment->id,
            'patient_id'     => auth()->id(),
            'doctor_id'      => $appointment->doctor_id,
            'rating'         => $request->rating,
            'review_text'    => $request->review_text,
        ]);

        return response()->json([
            'success' => true,
            'review'  => [
                'id'          => $review->id,
                'rating'      => $review->rating,
                'review_text' => $review->review_text,
                'created_at'  => $review->created_at->diffForHumans(),
            ],
        ]);
    }

    /** GET /doctors/{doctor}/reviews — list reviews for a doctor (JSON) */
    public function index(\App\Models\User $doctor): JsonResponse
    {
        abort_unless($doctor->role === 'doctor', 404);

        $reviews = DoctorReview::where('doctor_id', $doctor->id)
            ->with('patient')
            ->latest()
            ->paginate(10);

        $avgRating = DoctorReview::where('doctor_id', $doctor->id)->avg('rating');

        return response()->json([
            'average_rating' => round($avgRating, 1),
            'total_reviews'  => $reviews->total(),
            'reviews'        => $reviews->items(),
        ]);
    }
}
