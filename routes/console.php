<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Appointment Reminders (Feature R) ───────────────────────────────────────
// Runs every 30 minutes to dispatch 24h and 1h reminders
Schedule::command('reminders:appointments')->everyThirtyMinutes();
