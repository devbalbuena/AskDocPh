<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    /** GET /patient/dashboard */
    public function index(): View
    {
        $user = auth()->user();

        $upcomingAppointments = Appointment::where('patient_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', today())
            ->with('doctor')
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $bookmarksCount = $user->bookmarks()->count();

        $unreadNotifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $recentPosts = Post::with(['user', 'media', 'likes', 'moodTags.moodTag'])
            ->latest()
            ->limit(3)
            ->get();

        $todayAffirmation = \App\Models\DailyAffirmation::where('is_published', true)
            ->where('publish_at', '<=', now())
            ->latest('publish_at')
            ->first();

        return view('patient.dashboard', compact(
            'user',
            'upcomingAppointments',
            'bookmarksCount',
            'unreadNotifications',
            'recentPosts',
            'todayAffirmation',
        ));
    }
}
