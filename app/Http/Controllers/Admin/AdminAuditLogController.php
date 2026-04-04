<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\View\View;

class AdminAuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(): View
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(30);

        return view('admin.audit-logs.index', compact('logs'));
    }
}
