<?php

namespace App\Http\View\Composers;

use App\Models\Announcement;
use App\Models\AnnouncementDismissal;
use Illuminate\View\View;

class AnnouncementComposer
{
    public function compose(View $view): void
    {
        if (!auth()->check()) {
            $view->with('globalAnnouncements', collect());
            return;
        }

        $dismissed = AnnouncementDismissal::where('user_id', auth()->id())
            ->pluck('announcement_id');

        $announcements = Announcement::whereNotIn('id', $dismissed)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->get();

        $view->with('globalAnnouncements', $announcements);
    }
}
