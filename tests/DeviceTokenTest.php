<?php

use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;
use MarcioElias\LaravelNotifications\LaravelNotifications;
use MarcioElias\LaravelNotifications\Tests\Support\Helpers;
use MarcioElias\LaravelNotifications\Tests\Support\Models\User;

it('updates the device token for the user using the AWS SNS service', function () {
    $this->withoutExceptionHandling();

    Config::set('notifications.push_service_provider', 'aws_sns');

    Helpers::setupAwsEnv();

    $mockNotifications = Mockery::mock(LaravelNotifications::class);

    $endpointArn1 = 'arn:aws:sns:us-east-1:123456789012:endpoint/GCM/SomeEndpointArn';
    $endpointArn2 = 'arn:aws:sns:us-east-1:999999999999:endpoint/GCM/SomeEndpointArn';

    $deviceToken1 = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $deviceToken2 = 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY';

    $mockNotifications->shouldReceive('createEndpointArn')
        ->once()
        ->with(
            $deviceToken1,
            ['user_id' => 1]
        )
        ->andReturn($endpointArn1);

    $this->app->instance(LaravelNotifications::class, $mockNotifications);

    Sanctum::actingAs($user = Helpers::fakeUser());

    $response = $this->postJson(route('laravel-notifications.device_token'), [
        'device_token' => $deviceToken1,
        'custom_user_data' => ['user_id' => 1],
    ]);

    expect($response->status())->toBe(201);
    expect($response->json('message'))->toBe('Device token updated successfully');
    expect(User::find($user->id)->device_token)->toBe($deviceToken1);
    expect(User::find($user->id)->endpoint_arn)->toBe($endpointArn1);

    // if call again with the same device_token, it should not create a new endpointArn
    $mockNotifications->shouldNotReceive('createEndpointArn');

    $response = $this->postJson(route('laravel-notifications.device_token'), [
        'device_token' => $deviceToken1,
        'custom_user_data' => ['user_id' => 1],
    ]);

    expect($response->status())->toBe(200);
    expect($response->json('message'))->toBe('Device token already exists');
    expect(User::find($user->id)->device_token)->toBe($deviceToken1);
    expect(User::find($user->id)->endpoint_arn)->toBe($endpointArn1);

    // if call again but with a different device_toke, it should create a new endpointArn
    $mockNotifications->shouldReceive('createEndpointArn')
        ->once()
        ->with(
            $deviceToken2,
            ['user_id' => 1]
        )
        ->andReturn($endpointArn2);

    $response = $this->postJson(route('laravel-notifications.device_token'), [
        'device_token' => $deviceToken2,
        'custom_user_data' => ['user_id' => 1],
    ]);

    expect($response->status())->toBe(201);
    expect($response->json('message'))->toBe('Device token updated successfully');
    expect(User::find($user->id)->device_token)->toBe($deviceToken2);
    expect(User::find($user->id)->endpoint_arn)->toBe($endpointArn2);

});

it('updates the device token for the user using the OneSignal service', function() {
    Config::set('notifications.push_service_provider', 'onesignal');

    Sanctum::actingAs($user = Helpers::fakeUser());

    $response = $this->postJson(route('laravel-notifications.device_token'), [
        'device_token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        'custom_user_data' => ['user_id' => 1],
    ]);

    expect($response->status())->toBe(201);
    expect($response->json('message'))->toBe('Device token updated successfully');
    expect(User::find($user->id)->device_token)->toBe('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
    expect(User::find($user->id)->endpoint_arn)->toBe(null);
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
