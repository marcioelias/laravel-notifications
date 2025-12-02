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
        'alertable_type' => 'User',
        'alertable_id' => 1,
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

    expect($notification->alertable())->toBeInstanceOf(MorphTo::class);
});

it('tests the morphTo relationship with a User model', function () {
    $user = Helpers::fakeUser();

    $notification = Notification::factory()->create([
        'alertable_id' => $user->id,
        'alertable_type' => User::class,
    ]);

    expect($notification->alertable)->toBeInstanceOf(User::class);
    expect($notification->alertable->id)->toBe($user->id);
});

it('can create a notification with custom fields', function () {
    $user = Helpers::fakeUser();

    $notification = Notification::create([
        'title' => 'Test Notification',
        'body' => 'This is a test notification body.',
        'alertable_id' => $user->id,
        'alertable_type' => User::class,
        'notification_type' => 2,
        'category' => 'order',
        'priority' => 5,
        'related_id' => 123,
    ]);

    expect($notification->category)->toBe('order');
    expect($notification->priority)->toBe(5);
    expect($notification->related_id)->toBe(123);
    expect(DB::table('notifications')->where('id', $notification->id)->value('category'))->toBe('order');
});

it('can update a notification with custom fields using fillCustomFields method', function () {
    $user = Helpers::fakeUser();

    $notification = Notification::create([
        'title' => 'Test Notification',
        'body' => 'This is a test notification body.',
        'alertable_id' => $user->id,
        'alertable_type' => User::class,
        'notification_type' => 2,
    ]);

    $notification->fillCustomFields([
        'category' => 'order',
        'priority' => 5,
        'related_id' => 123,
    ])->save();

    expect($notification->fresh()->category)->toBe('order');
    expect($notification->fresh()->priority)->toBe(5);
    expect($notification->fresh()->related_id)->toBe(123);
});
