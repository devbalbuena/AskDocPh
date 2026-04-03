<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Appointment Reminders (Feature B) ───────────────────────────────────────
// Runs every 15 minutes to dispatch 24h and 1h reminders
Schedule::command('appointments:send-reminders')->everyFifteenMinutes();
