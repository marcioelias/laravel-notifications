<?php

use Illuminate\Support\Facades\Route;
use MarcioElias\LaravelNotifications\Http\Controllers\Api;

Route::prefix('api')->middleware(['auth:sanctum', 'api'])->group(function () {
    Route::name('laravel-notifications.')->group(function () {
        Route::post('device-token', Api\DeviceTokenController::class)->name('device_token');
        Route::get('notifications', [Api\NotificationController::class, 'index'])->name('index');
        Route::put('notification/{notification}/read', [Api\NotificationController::class, 'markAsRead'])->name('read');
        Route::put('notification/{notification}/unread', [Api\NotificationController::class, 'markAsUnread'])->name('unread');
    });
});
