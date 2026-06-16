<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\Shared\FeedController;
use App\Http\Controllers\Shared\NotificationController;
use App\Http\Controllers\Shared\GroupController;
use App\Http\Controllers\Shared\HelpRequestController;
use App\Http\Controllers\Shared\PostAnalyticsController;
use App\Http\Controllers\Shared\CommunityPollController;
use App\Http\Controllers\Shared\SettingsController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController;
use App\Http\Controllers\Patient\BookmarkController;
use App\Http\Controllers\Patient\BookmarkCollectionController;
use App\Http\Controllers\Patient\CrisisReportController;
use App\Http\Controllers\Patient\DoctorListController;
use App\Http\Controllers\Patient\MoodTrackerController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Doctor\DoctorScheduleController;
use App\Http\Controllers\Doctor\DoctorAppointmentController;
use App\Http\Controllers\Doctor\AppointmentNoteController;
use App\Http\Controllers\Doctor\ReferralController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDoctorApplicationController;
use App\Http\Controllers\Admin\AdminCrisisReportController;
use App\Http\Controllers\Admin\AdminAffirmationController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Shared\AnnouncementController;
use App\Http\Controllers\DoctorReviewController;
use App\Http\Controllers\AIChatController;
use Illuminate\Support\Facades\Route;

// ─── Public landing page ──────────────────────────────────────────────────────
Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');
Route::post('/contact', [\App\Http\Controllers\LandingController::class, 'contact'])->name('contact');

// ─── Profile ────────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified.email'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    // Breeze compatibility alias so existing sidebar links still work
    Route::get('/profile/edit', [ProfileController::class, 'show'])->name('profile.edit');
    Route::post('/profile/toggle-2fa', [ProfileController::class, 'toggle2FA'])->name('profile.toggle-2fa');
    Route::get('/profile/security', [ProfileController::class, 'security'])->name('profile.security');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ─── Shared Authenticated Routes ──────────────────────────────────────────────
Route::middleware(['auth', 'verified.email'])->group(function () {
    // Feed
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');
    Route::post('/posts', [FeedController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [FeedController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comments', [FeedController::class, 'comment'])->name('posts.comment');
    Route::post('/posts/{post}/report', [FeedController::class, 'report'])->name('posts.report');

    // Polls
    Route::post('/polls/{poll}/vote', [CommunityPollController::class, 'vote'])->name('polls.vote');

    // Announcements dismiss
    Route::post('/announcements/{announcement}/dismiss', [AnnouncementController::class, 'dismiss'])->name('announcements.dismiss');

    // Settings
    Route::post('/settings/dark-mode', [SettingsController::class, 'toggleDarkMode'])->name('settings.darkmode');


    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');

    // Resources (shared between patient, doctor, admin)
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');

    // User search (literal route MUST be before /users/{username} wildcard)
    Route::get('/users/search', [MessagingController::class, 'searchUsers'])->name('users.search');

    // Public user profiles
    Route::get('/users/{username}', [PublicProfileController::class, 'show'])->name('users.profile');

    // Follow / Unfollow
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');
    Route::get('/users/{user}/followers', [FollowController::class, 'followers'])->name('users.followers');
    Route::get('/users/{user}/following', [FollowController::class, 'following'])->name('users.following');

    // Verified-only Shared Routes
    Route::middleware('verified.id')->group(function () {
        // Messaging — IMPORTANT: literal routes BEFORE wildcard {conversation}
        Route::get('/messages/unread-count', [MessagingController::class, 'unreadCount'])->name('messages.unread-count');
        Route::get('/messages', [MessagingController::class, 'index'])->name('messages.index');
        Route::post('/messages/start', [MessagingController::class, 'start'])->name('messages.start');
        Route::get('/messages/{conversation}/poll', [MessagingController::class, 'poll'])->name('messages.poll');
        Route::post('/messages/{conversation}/send', [MessagingController::class, 'send'])->name('messages.send');
        Route::get('/messages/{conversation}', [MessagingController::class, 'show'])->name('messages.show');

        // Doctor Reviews
        Route::post('/doctor-reviews', [DoctorReviewController::class, 'store'])->name('doctor-reviews.store');

        // Group Communities
        Route::get('/communities', [GroupController::class, 'index'])->name('communities.index');
        Route::get('/communities/create', [GroupController::class, 'create'])->name('communities.create');
        Route::post('/communities', [GroupController::class, 'store'])->name('communities.store');
        Route::get('/communities/{group}', [GroupController::class, 'show'])->name('communities.show');
        Route::post('/communities/{group}/join', [GroupController::class, 'join'])->name('communities.join');
        Route::post('/communities/{group}/leave', [GroupController::class, 'leave'])->name('communities.leave');
        Route::post('/communities/{group}/post', [GroupController::class, 'createPost'])->name('communities.post');
        Route::post('/communities/{group}/polls', [CommunityPollController::class, 'store'])->name('communities.polls.store');
        Route::post('/communities/{group}/polls/{poll}/vote', [CommunityPollController::class, 'communityVote'])->name('communities.polls.vote');

        // Help Requests
        Route::get('/help-requests', [HelpRequestController::class, 'index'])->name('help-requests.index');
        Route::get('/help-requests/create', [HelpRequestController::class, 'create'])->name('help-requests.create');
        Route::post('/help-requests', [HelpRequestController::class, 'store'])->name('help-requests.store');
        Route::get('/help-requests/{helpRequest}', [HelpRequestController::class, 'show'])->name('help-requests.show');
        Route::post('/help-requests/{helpRequest}/accept', [HelpRequestController::class, 'accept'])->name('help-requests.accept');
        Route::post('/help-requests/{helpRequest}/decline', [HelpRequestController::class, 'decline'])->name('help-requests.decline');
        Route::post('/help-requests/{helpRequest}/resolve', [HelpRequestController::class, 'resolve'])->name('help-requests.resolve');
        Route::post('/help-requests/{helpRequest}/message', [HelpRequestController::class, 'sendMessage'])->name('help-requests.message');
    });

    // You can still view reviews without being verified
    Route::get('/doctors/{doctor}/reviews', [DoctorReviewController::class, 'index'])->name('doctor-reviews.index');

    // Post Analytics
    Route::get('/profile/analytics', [PostAnalyticsController::class, 'index'])->name('profile.analytics');

    // AI Chatbot
    Route::post('/ai-chat', [AIChatController::class, 'respond'])->name('ai.chat');
});

// ─── Patient Routes ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified.email', 'role:patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');

        // ID Verification
        Route::get('/id-verification', [\App\Http\Controllers\Patient\IdVerificationController::class, 'show'])->name('id-verification.notice');
        Route::post('/id-verification', [\App\Http\Controllers\Patient\IdVerificationController::class, 'store'])->name('id-verification.store');

        // Verified Patient Routes
        Route::middleware('verified.id')->group(function () {
            // Appointments
            Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
            Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
            Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
            Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

            // Doctors
            Route::get('/doctors', [DoctorListController::class, 'index'])->name('doctors.index');
            Route::get('/doctors/{doctor}/schedule', [DoctorListController::class, 'schedule'])->name('doctors.schedule');

            // Bookmarks & Collections
            Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
            Route::post('/bookmarks/{post}/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

            Route::get('/bookmarks/collections', [BookmarkCollectionController::class, 'index'])->name('bookmarks.collections.index');
            Route::post('/bookmarks/collections', [BookmarkCollectionController::class, 'store'])->name('bookmarks.collections.store');
            Route::get('/bookmarks/collections/{collection}', [BookmarkCollectionController::class, 'show'])->name('bookmarks.collections.show');
            Route::delete('/bookmarks/collections/{collection}', [BookmarkCollectionController::class, 'destroy'])->name('bookmarks.collections.destroy');
            Route::post('/bookmarks/collections/{collection}/add', [BookmarkCollectionController::class, 'addItem'])->name('bookmarks.collections.add');
            Route::delete('/bookmarks/collections/{collection}/items/{item}', [BookmarkCollectionController::class, 'removeItem'])->name('bookmarks.collections.remove');

            // Crisis
            Route::post('/crisis-reports', [CrisisReportController::class, 'store'])->name('crisis.store');

            // Mood Tracker
            Route::get('/mood', [MoodTrackerController::class, 'index'])->name('mood.index');
            Route::post('/mood', [MoodTrackerController::class, 'store'])->name('mood.store');
            Route::get('/mood/history', [MoodTrackerController::class, 'history'])->name('mood.history');

            // Doctor reviews from appointment
            Route::post('/appointments/{appointment}/review', [DoctorReviewController::class, 'store'])->name('appointments.review');
        });
    });

// ─── Doctor Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified.email', 'role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::post('/status/update', [DoctorDashboardController::class, 'updateStatus'])->name('status.update');

        // Schedule (literal routes MUST come before {schedule} wildcard)
        Route::get('/schedule', [DoctorScheduleController::class, 'index'])->name('schedule.index');
        Route::post('/schedule', [DoctorScheduleController::class, 'store'])->name('schedule.store');
        // Blocked dates — declared before wildcards so Laravel doesn't catch them as {schedule}
        Route::post('/schedule/block-date', [DoctorScheduleController::class, 'blockDate'])->name('schedule.blockDate');
        Route::delete('/schedule/unblock-date', [DoctorScheduleController::class, 'unblockDate'])->name('schedule.unblockDate');
        // Wildcard CRUD routes (must come AFTER literal routes above)
        Route::put('/schedule/{schedule}', [DoctorScheduleController::class, 'update'])->name('schedule.update');
        Route::delete('/schedule/{schedule}', [DoctorScheduleController::class, 'destroy'])->name('schedule.destroy');

        // Appointments
        Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/appointments/{appointment}/confirm', [DoctorAppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::post('/appointments/{appointment}/complete', [DoctorAppointmentController::class, 'complete'])->name('appointments.complete');
        Route::post('/appointments/{appointment}/cancel', [DoctorAppointmentController::class, 'cancel'])->name('appointments.cancel');

        // Appointment Notes
        Route::post('/appointments/{appointment}/notes', [AppointmentNoteController::class, 'store'])->name('appointments.notes.store');
        Route::put('/appointments/{appointment}/notes/{note}', [AppointmentNoteController::class, 'update'])->name('appointments.notes.update');
        Route::get('/appointments/{appointment}/notes/{note}', [AppointmentNoteController::class, 'show'])->name('appointments.notes.show');

        // Referrals
        Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
        Route::post('/referrals', [ReferralController::class, 'store'])->name('referrals.store');
        Route::post('/referrals/{referral}/accept', [ReferralController::class, 'accept'])->name('referrals.accept');
        Route::post('/referrals/{referral}/decline', [ReferralController::class, 'decline'])->name('referrals.decline');

        // Resources (doctor can create and delete their own)
        Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
        Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
        Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');

        // Calendar data endpoint — literal before wildcard
        Route::get('/schedule/calendar-data', [DoctorScheduleController::class, 'calendarData'])->name('schedule.calendar');
        Route::post('/schedule/block-date-db', [DoctorScheduleController::class, 'blockDateDb'])->name('schedule.blockDateDb');
        Route::delete('/schedule/unblock-date-db', [DoctorScheduleController::class, 'unblockDateDb'])->name('schedule.unblockDateDb');

        // Patient mood history (for doctors viewing before appointments)
        Route::get('/appointments/{appointment}/patient-mood', [DoctorAppointmentController::class, 'patientMoodHistory'])->name('appointments.patientMood');
    });

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified.email', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // ID Verifications
        Route::get('/id-verifications', [\App\Http\Controllers\Admin\IdVerificationController::class, 'index'])->name('id-verification.index');
        Route::put('/id-verifications/{user}', [\App\Http\Controllers\Admin\IdVerificationController::class, 'update'])->name('id-verification.update');

        // Users
        Route::resource('/users', AdminUserController::class)->only(['index', 'show', 'destroy']);

        // Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AdminAuditLogController::class, 'index'])->name('audit-logs.index');
        // Doctor Applications
        Route::resource('/doctor-applications', AdminDoctorApplicationController::class)->only(['index', 'show']);
        Route::post('/doctor-applications/{application}/approve', [AdminDoctorApplicationController::class, 'approve'])->name('doctor-applications.approve');
        Route::post('/doctor-applications/{application}/reject', [AdminDoctorApplicationController::class, 'reject'])->name('doctor-applications.reject');

        // Crisis Reports
        Route::get('/crisis-reports', [AdminCrisisReportController::class, 'index'])->name('crisis.index');
        Route::post('/crisis-reports/{report}/respond', [AdminCrisisReportController::class, 'respond'])->name('crisis.respond');
        Route::post('/crisis-reports/{report}/resolve', [AdminCrisisReportController::class, 'resolve'])->name('crisis.resolve');

        // Feed
        Route::get('/feed', [\App\Http\Controllers\Admin\AdminFeedController::class, 'index'])->name('feed');

        // Affirmations
        Route::resource('/affirmations', AdminAffirmationController::class);

        // Reports Export
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/users', [AdminReportController::class, 'exportUsers'])->name('reports.export.users');
        Route::get('/reports/export/appointments', [AdminReportController::class, 'exportAppointments'])->name('reports.export.appointments');
        Route::get('/reports/export/crisis-reports', [AdminReportController::class, 'exportCrisisReports'])->name('reports.export.crisis');
        Route::get('/reports/export/users-pdf', [AdminReportController::class, 'exportUsersPdf'])->name('reports.export.users-pdf');
        Route::get('/reports/export/appointments-pdf', [AdminReportController::class, 'exportAppointmentsPdf'])->name('reports.export.appointments-pdf');
        Route::get('/reports/export/crisis-reports-pdf', [AdminReportController::class, 'exportCrisisPdf'])->name('reports.export.crisis-pdf');

        // Announcements
        Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

require __DIR__.'/auth.php';
