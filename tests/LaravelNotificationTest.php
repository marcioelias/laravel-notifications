<?php

use MarcioElias\LaravelNotifications\Facades\LaravelNotifications;
use MarcioElias\LaravelNotifications\Models\Notification;

it('can mark a notification as readed', function () {
    $notificationData = [
        'title' => 'Test Notification',
        'body' => 'This is a test notification body.',
        'notifiable_type' => 'User',
        'notifiable_id' => 1,
    ];

    $user = (object) [
        'id' => 1,
        'getKey' => function () {
            return 1;
        },
        'getMorphClass' => function () {
            return 'User';
        },
    ];

    $notification = Notification::factory()->create($notificationData);

    LaravelNotifications::readNotification($notification);

    expect($notification->fresh()->readed)->toBeTrue();
});
