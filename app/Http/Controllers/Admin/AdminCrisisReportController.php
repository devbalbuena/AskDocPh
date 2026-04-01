<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrisisReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCrisisReportController extends Controller
{
    /** GET /admin/crisis-reports — list all reports with status filter */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $reports = CrisisReport::with('user')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.crisis-reports.index', compact('reports', 'status'));
    }

    /** POST /admin/crisis-reports/{report}/respond */
    public function respond(CrisisReport $report): RedirectResponse
    {
        $report->update([
            'status'       => 'responding',
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->route('admin.crisis.index')
            ->with('success', 'Marked as responding.');
    }

    /** POST /admin/crisis-reports/{report}/resolve */
    public function resolve(CrisisReport $report): RedirectResponse
    {
        $report->update([
            'status'      => 'resolved',
            'resolved_at' => now(),
        ]);

        return redirect()->route('admin.crisis.index')
            ->with('success', 'Crisis report resolved.');
    }
}
