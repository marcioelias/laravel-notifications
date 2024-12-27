<?php

use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use MarcioElias\LaravelNotifications\LaravelNotifications;
use MarcioElias\LaravelNotifications\Tests\Support\Helpers;
use MarcioElias\LaravelNotifications\Tests\Support\Models\User;

it('updates the device token for the user using the AWS SNS service', function () {
    Config::set('notifications.push_service_provider', 'aws_sns');

    Helpers::setupAwsEnv();

    $mockNotifications = Mockery::mock(LaravelNotifications::class);

    $mockNotifications->shouldReceive('createEndpointArn')
        ->once()
        ->with(
            'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            ['user_id' => 1]
        )
        ->andReturn('arn:aws:sns:us-east-1:123456789012:endpoint/GCM/SomeEndpointArn');

    $this->app->instance(LaravelNotifications::class, $mockNotifications);

    Sanctum::actingAs($user = Helpers::fakeUser());

    $response = $this->postJson(route('laravel-notifications.device_token'), [
        'device_token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        'custom_user_data' => ['user_id' => 1],
    ]);

    expect($response->status())->toBe(201);
    expect($response->json('message'))->toBe('Device token updated successfully');
    expect(User::find($user->id)->device_token)->toBe('arn:aws:sns:us-east-1:123456789012:endpoint/GCM/SomeEndpointArn');

    Config::set('notifications.push_service_provider', 'onesignal');

    $response = $this->postJson(route('laravel-notifications.device_token'), [
        'device_token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        'custom_user_data' => ['user_id' => 1],
    ]);

    expect($response->status())->toBe(201);
    expect($response->json('message'))->toBe('Device token updated successfully');
    expect(User::find($user->id)->device_token)->toBe('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
});

it('returns an exception when update user device token fails', function () {

    Config::set('notifications.push_service_provider', 'aws_sns');

    Helpers::setupAwsEnv();

    Sanctum::actingAs($user = Helpers::fakeUser());

    $response = $this->postJson(route('laravel-notifications.device_token'), [
        'device_token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        'custom_user_data' => ['user_id' => str_repeat('a', 257)],
    ]);

    expect($response->status())->toBe(500);
    expect($response->json('message'))->toBe('CustomUserData must be less than 256 characters');
    expect(User::find($user->id)->device_token)->toBeNull();
});
