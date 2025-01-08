<?php

use MarcioElias\LaravelNotifications\Facades\LaravelNotifications;
use MarcioElias\LaravelNotifications\Models\Notification;
use MarcioElias\LaravelNotifications\Tests\Support\Helpers;

it('can mark a notification as readed', function () {
    $notificationData = [
        'title' => 'Test Notification',
        'body' => 'This is a test notification body.',
        'alertable_type' => 'User',
        'alertable_id' => 1,
    ];

    Helpers::fakeUser();

    $notification = Notification::factory()->create($notificationData);

    LaravelNotifications::readNotification($notification);

    expect($notification->fresh()->readed)->toBeTrue();
});
