<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an admin action to the database.
     *
     * @param string $action A brief identifier for the action (e.g., 'approve_doctor', 'delete_user')
     * @param string|null $description Optional context (e.g., 'Approved application for Dr. Smith')
     */
    public static function log(string $action, ?string $description = null): void
    {
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'description' => $description,
            'ip_address'  => Request::ip(),
        ]);
    }
}
