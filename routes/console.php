<?php

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schedule;

Schedule::command('documents:check-expirations')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/expiration-check.log'));

// Invio email promemoria scadenze: abilitato e orario configurabili dalle Impostazioni Email
$emailSettings = AppSetting::instance();

if ($emailSettings->expiry_reminder_enabled ?? true) {
    $time = preg_match('/^\d{2}:\d{2}$/', $emailSettings->expiry_reminder_time ?? '')
        ? $emailSettings->expiry_reminder_time
        : '08:00';

    Schedule::command('email:send-expiry-reminder')
        ->dailyAt($time)
        ->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/expiry-email.log'));
}
