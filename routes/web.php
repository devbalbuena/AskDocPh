<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\Shared\FeedController;
use App\Http\Controllers\Shared\NotificationController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController;
use App\Http\Controllers\Patient\BookmarkController;
use App\Http\Controllers\Patient\CrisisReportController;
use App\Http\Controllers\Patient\DoctorListController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Doctor\DoctorScheduleController;
use App\Http\Controllers\Doctor\DoctorAppointmentController;
use App\Http\Controllers\Doctor\AppointmentNoteController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDoctorApplicationController;
use App\Http\Controllers\Admin\AdminCrisisReportController;
use App\Http\Controllers\Admin\AdminAffirmationController;
use Illuminate\Support\Facades\Route;

// ─── Public landing page ──────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Profile ────────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    // Breeze compatibility alias so existing sidebar links still work
    Route::get('/profile/edit', [ProfileController::class, 'show'])->name('profile.edit');
});


// ─── Shared Authenticated Routes ──────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Feed
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');
    Route::post('/posts', [FeedController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [FeedController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comments', [FeedController::class, 'comment'])->name('posts.comment');


    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');

    // Resources (shared between patient, doctor, admin)
    Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');

    // Public user profiles
    Route::get('/users/{username}', [PublicProfileController::class, 'show'])->name('users.profile');

    // Follow / Unfollow
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');
    Route::get('/users/{user}/followers', [FollowController::class, 'followers'])->name('users.followers');
    Route::get('/users/{user}/following', [FollowController::class, 'following'])->name('users.following');
});

// ─── Patient Routes ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');

        // Appointments
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

        // Doctors
        Route::get('/doctors', [DoctorListController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/{doctor}/schedule', [DoctorListController::class, 'schedule'])->name('doctors.schedule');

        // Bookmarks
        Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
        Route::post('/bookmarks/{post}/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');

        // Crisis
        Route::post('/crisis-reports', [CrisisReportController::class, 'store'])->name('crisis.store');
    });

// ─── Doctor Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

        // Schedule (resource-style)
        Route::get('/schedule', [DoctorScheduleController::class, 'index'])->name('schedule.index');
        Route::post('/schedule', [DoctorScheduleController::class, 'store'])->name('schedule.store');
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

        // Resources (doctor can create and delete their own)
        Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
        Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
        Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');
    });

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('/users', AdminUserController::class)->only(['index', 'show', 'destroy']);

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
    });

require __DIR__.'/auth.php';
