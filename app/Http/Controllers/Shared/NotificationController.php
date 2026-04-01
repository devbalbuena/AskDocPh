<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /** GET /notifications — list the authenticated user's notifications */
    public function index(): View
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->with('actor')
            ->latest()
            ->paginate(20);

        // Mark all as read on view
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('shared.notifications.index', compact('notifications'));
    }

    /** POST /notifications/{id}/read — mark a single notification as read (Axios) */
    public function markRead(int $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /** GET unread count — for navbar badge (Axios polling) */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}
