<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementDismissal;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{
    public function dismiss(Announcement $announcement): JsonResponse
    {
        // Silently ignore duplicates (unique constraint)
        AnnouncementDismissal::firstOrCreate([
            'announcement_id' => $announcement->id,
            'user_id'         => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }
}
