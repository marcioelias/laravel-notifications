<?php

use Laravel\Sanctum\Sanctum;
use MarcioElias\LaravelNotifications\Models\Notification;
use MarcioElias\LaravelNotifications\Tests\Support\Helpers;
use MarcioElias\LaravelNotifications\Tests\Support\Models\User;

it('must return a list of notifications', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($user = Helpers::fakeUser());

    Notification::factory(count: 5)->create([
        'alertable_type' => User::class,
        'alertable_id' => $user->id,
        'readed' => false,
    ]);

    $response = $this->getJson(route('laravel-notifications.index'));

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray()->toHaveCount(5);
});

it('must math notification resource structure', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($user = Helpers::fakeUser());

    $notification = Notification::factory(count: 1)->create([
        'alertable_type' => User::class,
        'alertable_id' => $user->id,
        'readed' => false,
    ])->first();

    $response = $this->getJson(route('laravel-notifications.index'));

    expect($response->status())->toBe(200);

    $expectedData = [
        'id' => $notification->id,
        'title' => $notification->title,
        'body' => $notification->body,
        'notification_type' => $notification->notification_type->value,
        'readed' => $notification->readed,
        'created_at' => $notification->created_at->toIsoString(),
        'updated_at' => $notification->updated_at->toIsoString(),
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
        ],
    ];
    expect($response->json('data')[0])->toEqual($expectedData);
});

it('must paginate notifications', function () {
    $this->withoutExceptionHandling();
    config()->set('notifications.api.pagination', 2);
    Sanctum::actingAs($user = Helpers::fakeUser());

    Notification::factory(count: 3)->create([
        'alertable_type' => User::class,
        'alertable_id' => $user->id,
        'readed' => false,
    ]);

    $response = $this->getJson(route('laravel-notifications.index'));

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray()->toHaveCount(2);
    expect($response->json('links'))->toBeArray()->toHaveCount(4);
    expect($response->json('links'))->toHaveKey('first');
    expect($response->json('links'))->toHaveKey('last');
    expect($response->json('links'))->toHaveKey('prev');
    expect($response->json('links'))->toHaveKey('next');
});

it('must mark a notification as readed', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($user = Helpers::fakeUser());

    $notification = Notification::factory(count: 1)->create([
        'alertable_type' => User::class,
        'alertable_id' => $user->id,
        'readed' => false,
    ])->first();

    expect($notification->readed)->toBe(false);

    $response = $this->putJson(route('laravel-notifications.read', [
        'notification' => $notification,
    ]));

    expect($response->status())->toBe(202);

    $notification->refresh();
    expect($notification->readed)->toBe(true);
});

it('must mark a notification as unreaded', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($user = Helpers::fakeUser());

    $notification = Notification::factory(count: 1)->create([
        'alertable_type' => User::class,
        'alertable_id' => $user->id,
        'readed' => true,
    ])->first();

    expect($notification->readed)->toBe(true);

    $response = $this->putJson(route('laravel-notifications.unread', [
        'notification' => $notification,
    ]));

    expect($response->status())->toBe(202);

    $notification->refresh();
    expect($notification->readed)->toBe(false);
});

it('must mark all notifications as read', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($user = Helpers::fakeUser());

    Notification::factory(count: 3)->create([
        'alertable_type' => User::class,
        'alertable_id' => $user->id,
        'readed' => false,
    ]);

    $response = $this->putJson(route('laravel-notifications.read-all'));

    expect($response->status())->toBe(202);

    $notifications = Notification::all();
    expect($notifications)->toHaveCount(3);
    foreach ($notifications as $notification) {
        expect($notification->readed)->toBe(true);
    }
});

it('must return a count of all unread notifications', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($user = Helpers::fakeUser());

    Notification::factory(count: 3)->create([
        'alertable_type' => User::class,
        'alertable_id' => $user->id,
        'readed' => false,
    ]);

    $response = $this->getJson(route('laravel-notifications.unread-count'));

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray()->toHaveCount(1);
    expect($response->json('data.total'))->toBe(3);
});
