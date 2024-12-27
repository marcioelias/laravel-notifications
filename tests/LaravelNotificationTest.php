<?php

use MarcioElias\LaravelNotifications\Facades\LaravelNotifications;
use MarcioElias\LaravelNotifications\Models\Notification;
use MarcioElias\LaravelNotifications\Tests\Support\Helpers;

it('can mark a notification as readed', function () {
    $notificationData = [
        'title' => 'Test Notification',
        'body' => 'This is a test notification body.',
        'notifiable_type' => 'User',
        'notifiable_id' => 1,
    ];

    Helpers::fakeUser();

    $notification = Notification::factory()->create($notificationData);

    LaravelNotifications::readNotification($notification);

    expect($notification->fresh()->readed)->toBeTrue();
});
