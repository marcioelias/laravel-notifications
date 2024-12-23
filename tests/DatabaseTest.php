<?php

use Illuminate\Support\Facades\DB;
use MarcioElias\LaravelNotifications\Models\Notification;

it('can create a record of a notification on the database', function () {
    $notificationData = [
        'title' => 'Test Notification',
        'body' => 'This is a test notification body.',
    ];

    $notification = Notification::factory()->create($notificationData);

    expect($notification)->toBeInstanceOf(Notification::class);

    expect($notification->title)->toBe('Test Notification');
    expect($notification->body)->toBe('This is a test notification body.');

    expect(DB::table('notifications')->where($notificationData)->exists())->toBeTrue();
});
