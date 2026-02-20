<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('documents:check-expirations')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/expiration-check.log'));

Schedule::command('email:send-expiry-reminder')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/expiry-email.log'));
