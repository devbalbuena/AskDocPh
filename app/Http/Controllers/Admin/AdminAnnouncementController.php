<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::withCount('dismissals')
            ->latest()
            ->get();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'      => 'required|string|max:100',
            'message'    => 'required|string|max:500',
            'type'       => 'required|in:info,warning,urgent',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $announcement = Announcement::create([
            'admin_id'   => auth()->id(),
            'title'      => $request->title,
            'message'    => $request->message,
            'type'       => $request->type,
            'expires_at' => $request->expires_at,
        ]);

        return response()->json([
            'success'      => true,
            'announcement' => [
                'id'         => $announcement->id,
                'title'      => $announcement->title,
                'message'    => $announcement->message,
                'type'       => $announcement->type,
                'expires_at' => $announcement->expires_at?->format('M d, Y H:i'),
            ],
        ]);
    }

    public function destroy(Announcement $announcement): JsonResponse
    {
        $announcement->delete();

        return response()->json(['success' => true]);
    }
}
