<?php

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schedule;

Schedule::command('documents:check-expirations')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/expiration-check.log'));

// Invio email promemoria scadenze: abilitato e orario configurabili dalle Impostazioni Email
// Il try/catch evita errori durante il build (quando il DB non è disponibile)
try {
    $emailSettings = AppSetting::instance();
    $reminderEnabled = $emailSettings->expiry_reminder_enabled ?? true;
    $reminderTime = preg_match('/^\d{2}:\d{2}$/', $emailSettings->expiry_reminder_time ?? '')
        ? $emailSettings->expiry_reminder_time
        : '08:00';
} catch (\Throwable $e) {
    $reminderEnabled = true;
    $reminderTime = '08:00';
}

if ($reminderEnabled) {
    Schedule::command('email:send-expiry-reminder')
        ->dailyAt($reminderTime)
        ->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/expiry-email.log'));
}
