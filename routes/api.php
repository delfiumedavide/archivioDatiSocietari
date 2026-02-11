<?php

use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('push-subscriptions', [PushSubscriptionController::class, 'store']);
    Route::delete('push-subscriptions', [PushSubscriptionController::class, 'destroy']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
});
