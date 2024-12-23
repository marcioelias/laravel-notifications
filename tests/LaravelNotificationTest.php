<?php

use MarcioElias\LaravelNotifications\Facades\LaravelNotifications;
use MarcioElias\LaravelNotifications\Models\Notification;

it('can mark a notification as readed', function() {
    $notification = Notification::factory()->create();

    LaravelNotifications::readNotification($notification);

    expect($notification->fresh()->readed)->toBeTrue();
});
