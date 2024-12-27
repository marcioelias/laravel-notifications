<?php

use Laravel\Sanctum\Sanctum;
use MarcioElias\LaravelNotifications\Models\Notification;
use MarcioElias\LaravelNotifications\Tests\Support\Helpers;
use MarcioElias\LaravelNotifications\Tests\Support\Models\User;

it('must return a list of notifications', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($user = Helpers::fakeUser());

    Notification::factory(count: 5)->create([
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
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
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
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
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
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

it('update notification readed property', function () {
    $this->withoutExceptionHandling();
    Sanctum::actingAs($user = Helpers::fakeUser());

    $notification = Notification::factory(count: 1)->create([
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'readed' => false,
    ])->first();

    expect($notification->readed)->toBe(false);

    $response = $this->putJson(route('laravel-notifications.update', [
        'notification' => $notification,
        'readed' => true,
    ]));

    expect($response->status())->toBe(202);

    $notification->refresh();
    expect($notification->readed)->toBe(true);

    $response = $this->putJson(route('laravel-notifications.update', [
        'notification' => $notification,
        'readed' => false,
    ]));

    expect($response->status())->toBe(202);

    $notification->refresh();
    expect($notification->readed)->toBe(false);
});
