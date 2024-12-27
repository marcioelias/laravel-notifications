<?php

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use MarcioElias\LaravelNotifications\Models\Notification;
use MarcioElias\LaravelNotifications\Tests\Support\Helpers;
use MarcioElias\LaravelNotifications\Tests\Support\Models\User;

it('can create a record of a notification on the database', function () {
    $notificationData = [
        'title' => 'Test Notification',
        'body' => 'This is a test notification body.',
        'notifiable_type' => 'User',
        'notifiable_id' => 1,
    ];

    Sanctum::actingAs($user = Helpers::fakeUser());

    $notification = Notification::factory()->create($notificationData);

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->title)->toBe('Test Notification');
    expect($notification->body)->toBe('This is a test notification body.');

    expect(DB::table('notifications')->where($notificationData)->exists())->toBeTrue();
});

it('asserts morphTo relationship is properly configured', function () {
    $notification = new Notification;

    expect($notification->notifiable())->toBeInstanceOf(MorphTo::class);
});

it('tests the morphTo relationship with a User model', function () {
    $user = Helpers::fakeUser();

    $notification = Notification::factory()->create([
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
    ]);

    expect($notification->notifiable)->toBeInstanceOf(User::class);
    expect($notification->notifiable->id)->toBe($user->id);
});
