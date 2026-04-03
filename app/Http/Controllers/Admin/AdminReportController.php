<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CrisisReport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    /** GET /admin/reports */
    public function index(): View
    {
        $totalUsers        = User::whereIn('role', ['patient', 'doctor'])->count();
        $totalAppointments = Appointment::count();
        $totalCrisis       = CrisisReport::count();

        return view('admin.reports.index', compact('totalUsers', 'totalAppointments', 'totalCrisis'));
    }

    // ─── CSV Exports ──────────────────────────────────────────────────────────

    /** GET /admin/reports/export/users */
    public function exportUsers(Request $request): Response
    {
        $from = $request->from ?? null;
        $to   = $request->to ?? null;

        $query = User::whereIn('role', ['patient', 'doctor'])->orderBy('created_at', 'desc');
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to)   $query->whereDate('created_at', '<=', $to);

        $users = $query->get();

        $csv  = "ID,First Name,Last Name,Email,Username,Role,Status,Registered\n";
        foreach ($users as $u) {
            $status = $u->role === 'doctor' ? $u->doctor_status : 'N/A';
            $csv   .= '"' . implode('","', [
                $u->id, $u->fname, $u->lname, $u->email, $u->username,
                $u->role, $status, $u->created_at->format('Y-m-d'),
            ]) . '"' . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /** GET /admin/reports/export/appointments */
    public function exportAppointments(Request $request): Response
    {
        $from = $request->from ?? null;
        $to   = $request->to ?? null;

        $query = Appointment::with(['doctor', 'patient'])->orderBy('appointment_date', 'desc');
        if ($from) $query->whereDate('appointment_date', '>=', $from);
        if ($to)   $query->whereDate('appointment_date', '<=', $to);

        $appointments = $query->get();

        $csv = "ID,Patient,Doctor,Date,Start Time,End Time,Type,Status,Reason\n";
        foreach ($appointments as $a) {
            $csv .= '"' . implode('","', [
                $a->id,
                $a->patient?->display_name ?? 'N/A',
                $a->doctor?->display_name ?? 'N/A',
                $a->appointment_date,
                $a->start_time,
                $a->end_time,
                $a->type ?? '',
                $a->status ?? '',
                str_replace('"', '""', $a->reason ?? ''),
            ]) . '"' . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="appointments_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /** GET /admin/reports/export/crisis-reports */
    public function exportCrisisReports(Request $request): Response
    {
        $from = $request->from ?? null;
        $to   = $request->to ?? null;

        $query = CrisisReport::with('user')->latest();
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to)   $query->whereDate('created_at', '<=', $to);

        $reports = $query->get();

        $csv = "ID,Patient,Severity,Status,Description,Submitted At\n";
        foreach ($reports as $r) {
            $csv .= '"' . implode('","', [
                $r->id,
                $r->user?->display_name ?? 'N/A',
                $r->severity ?? '',
                $r->status ?? '',
                str_replace('"', '""', $r->description ?? ''),
                $r->created_at->format('Y-m-d H:i'),
            ]) . '"' . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="crisis_reports_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    // ─── PDF Exports ──────────────────────────────────────────────────────────

    /** GET /admin/reports/export/users-pdf */
    public function exportUsersPdf(Request $request)
    {
        $from  = $request->from ?? null;
        $to    = $request->to ?? null;
        $query = User::whereIn('role', ['patient', 'doctor'])->orderBy('created_at', 'desc');
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to)   $query->whereDate('created_at', '<=', $to);
        $users = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf.users', compact('users', 'from', 'to'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('users_report_' . now()->format('Y-m-d') . '.pdf');
    }

    /** GET /admin/reports/export/appointments-pdf */
    public function exportAppointmentsPdf(Request $request)
    {
        $from  = $request->from ?? null;
        $to    = $request->to ?? null;
        $query = Appointment::with(['doctor', 'patient'])->orderBy('appointment_date', 'desc');
        if ($from) $query->whereDate('appointment_date', '>=', $from);
        if ($to)   $query->whereDate('appointment_date', '<=', $to);
        $appointments = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf.appointments', compact('appointments', 'from', 'to'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('appointments_report_' . now()->format('Y-m-d') . '.pdf');
    }

    /** GET /admin/reports/export/crisis-reports-pdf */
    public function exportCrisisPdf(Request $request)
    {
        $from  = $request->from ?? null;
        $to    = $request->to ?? null;
        $query = CrisisReport::with('user')->latest();
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to)   $query->whereDate('created_at', '<=', $to);
        $reports = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf.crisis', compact('reports', 'from', 'to'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('crisis_reports_' . now()->format('Y-m-d') . '.pdf');
    }
}
