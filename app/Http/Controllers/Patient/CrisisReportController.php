<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\CrisisReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrisisReportController extends Controller
{
    /** POST /patient/crisis-reports — submit a crisis report (Axios JSON) */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'description' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $report = CrisisReport::create([
            'user_id'     => auth()->id(),
            'description' => $request->description,
            'status'      => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your report has been submitted. Our team will reach out to you shortly.',
            'report'  => [
                'id'         => $report->id,
                'created_at' => $report->created_at->toDateTimeString(),
            ],
        ]);
    }
}
