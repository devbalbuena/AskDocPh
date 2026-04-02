<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use Illuminate\Http\JsonResponse;
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

        $blockedDates = session('blocked_dates', []);

        return view('doctor.schedule.index', compact('schedules', 'blockedDates'));
    }

    /** POST /doctor/schedule — add a new availability slot (JSON) */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'day_of_week'           => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time'            => ['required', 'date_format:H:i'],
            'end_time'              => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,30,45,60'],
            'buffer_minutes'        => ['nullable', 'integer', 'in:0,5,10,15,30'],
        ]);

        // Overlap check
        $overlap = DoctorSchedule::where('doctor_id', auth()->id())
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($inner) use ($request) {
                      $inner->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                  });
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot overlaps with an existing slot on this day.',
            ], 422);
        }

        $buffer    = (int) ($request->buffer_minutes ?? 0);
        $duration  = (int) $request->slot_duration_minutes + $buffer;

        $slot = DoctorSchedule::create([
            'doctor_id'             => auth()->id(),
            'day_of_week'           => $request->day_of_week,
            'start_time'            => $request->start_time,
            'end_time'              => $request->end_time,
            'slot_duration_minutes' => $duration,
            'is_available'          => true,
        ]);

        return response()->json([
            'success'         => true,
            'message'         => 'Schedule slot added.',
            'slot'            => $slot,
            'base_duration'   => (int) $request->slot_duration_minutes,
            'buffer_minutes'  => $buffer,
        ]);
    }

    /** PUT /doctor/schedule/{schedule} — edit a slot (JSON) */
    public function update(Request $request, DoctorSchedule $schedule): JsonResponse
    {
        abort_unless($schedule->doctor_id === auth()->id(), 403);

        $request->validate([
            'start_time'            => ['required', 'date_format:H:i'],
            'end_time'              => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,30,45,60'],
            'buffer_minutes'        => ['nullable', 'integer', 'in:0,5,10,15,30'],
            'is_available'          => ['nullable', 'boolean'],
        ]);

        // Overlap check (excluding current slot)
        $overlap = DoctorSchedule::where('doctor_id', auth()->id())
            ->where('day_of_week', $schedule->day_of_week)
            ->where('id', '!=', $schedule->id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($inner) use ($request) {
                      $inner->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                  });
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot overlaps with an existing slot on this day.',
            ], 422);
        }

        $buffer   = (int) ($request->buffer_minutes ?? 0);
        $duration = (int) $request->slot_duration_minutes + $buffer;

        $schedule->update([
            'start_time'            => $request->start_time,
            'end_time'              => $request->end_time,
            'slot_duration_minutes' => $duration,
            'is_available'          => $request->boolean('is_available', $schedule->is_available),
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Schedule slot updated.',
            'slot'           => $schedule->fresh(),
            'base_duration'  => (int) $request->slot_duration_minutes,
            'buffer_minutes' => $buffer,
        ]);
    }

    /** DELETE /doctor/schedule/{schedule} — remove a slot (JSON) */
    public function destroy(DoctorSchedule $schedule): JsonResponse
    {
        abort_unless($schedule->doctor_id === auth()->id(), 403);

        $schedule->delete();

        return response()->json(['success' => true, 'message' => 'Schedule slot removed.']);
    }

    /** POST /doctor/schedule/block-date — store a blocked date in session */
    public function blockDate(Request $request): JsonResponse
    {
        $request->validate([
            'date'   => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $entry = [
            'id'     => uniqid(),
            'date'   => $request->date,
            'reason' => $request->reason ?? '',
        ];

        session()->push('blocked_dates', $entry);

        return response()->json(['success' => true, 'entry' => $entry]);
    }

    /** DELETE /doctor/schedule/unblock-date — remove a blocked date from session by date value */
    public function unblockDate(Request $request): JsonResponse
    {
        $date    = $request->input('date');

        \Log::info('Unblock request', [
            'request_data' => $request->all(),
            'date_received' => $date,
            'session_data'  => session('blocked_dates'),
        ]);

        $blocked = session('blocked_dates', []);
        $blocked = array_filter($blocked, function ($item) use ($date) {
            return ($item['date'] ?? '') !== $date;
        });
        session(['blocked_dates' => array_values($blocked)]);

        return response()->json(['success' => true]);
    }
}
