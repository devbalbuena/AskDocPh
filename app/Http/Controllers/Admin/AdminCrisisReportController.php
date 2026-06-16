<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrisisReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCrisisReportController extends Controller
{
    /** GET /admin/crisis-reports — list all reports with status filter */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $reports = CrisisReport::with(['user', 'respondedBy'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.crisis-reports.index', compact('reports', 'status'));
    }

    /** POST /admin/crisis-reports/{report}/respond — returns JSON for Axios */
    public function respond(CrisisReport $report): JsonResponse
    {
        $report->update([
            'status'       => 'responding',
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return response()->json(['success' => true, 'status' => 'responding']);
    }

    /** POST /admin/crisis-reports/{report}/resolve — returns JSON for Axios */
    public function resolve(CrisisReport $report): JsonResponse
    {
        $report->update([
            'status'      => 'resolved',
            'resolved_at' => now(),
        ]);

        return response()->json(['success' => true, 'status' => 'resolved']);
    }
}
