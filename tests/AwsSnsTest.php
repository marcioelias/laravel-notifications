<?php

use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use MarcioElias\LaravelNotifications\LaravelNotifications;
use MarcioElias\LaravelNotifications\Tests\Support\Helpers;

it('throws an exception if push client is not found', function () {
    Config::set('notifications.push_service_provider', 'unknown_provider');

    $notifications = new LaravelNotifications;

    $user = Helpers::fakeUser();

    $notifications->sendPush(
        $user,
        'title',
        'body',
        ['alert' => 'Hello, world!']
    );
})->throws(Exception::class, 'Push client not found');

it('returns a valid SnsClient instance using reflection', function () {
    Helpers::setupAwsEnv();

    $notifications = new LaravelNotifications;

    $reflection = new ReflectionClass(LaravelNotifications::class);
    $method = $reflection->getMethod('getSnsClient');
    $method->setAccessible(true);

    $snsClient = $method->invoke($notifications);

    expect($snsClient)->toBeInstanceOf(SnsClient::class);
});

it('sends push notifications using AWS SNS', function () {
    $snsClientMock = Mockery::mock(SnsClient::class);

    $payload = [
        'GCM' => json_encode([
            'fcmV1Message' => [
                'message' => [
                    'notification' => [
                        'title' => 'title',
                        'body' => 'body',
                    ],
                ],
            ],
            'data' => ['alert' => 'Hello, world!'],
        ]),
        'APNS' => json_encode([
            'aps' => [
                'alert' => [
                    'title' => 'title',
                    'body' => 'body',
                ],
                'sound' => 'default',
            ],
            'data' => ['alert' => 'Hello, world!'],
        ]),
        'APNS_SANDBOX' => json_encode([
            'aps' => [
                'alert' => [
                    'title' => 'title',
                    'body' => 'body',
                ],
                'sound' => 'default',
            ],
            'data' => ['alert' => 'Hello, world!'],
        ]),
        'ADM' => json_encode([
            'notification' => [
                'title' => 'title',
                'body' => 'body',
            ],
            'data' => ['alert' => 'Hello, world!'],
        ]),
    ];

    $user = Helpers::fakeUser();

    $snsClientMock->shouldReceive('publish')
        ->once()
        ->with([
            'TargetArn' => $user->getDestination(),
            'Message' => json_encode($payload),
            'MessageStructure' => 'json',
        ])
        ->andReturn(['MessageId' => '1234']);

    Config::set('notifications.push_service_provider', 'aws_sns');

    $notifications = Mockery::mock(LaravelNotifications::class)->makePartial();
    $notifications->shouldAllowMockingProtectedMethods();
    $notifications->shouldReceive('getPushClient')->andReturn($snsClientMock);

    $result = null;

    try {
        $notifications->sendPush(
            $user,
            'title',
            'body',
            ['alert' => 'Hello, world!']
        );

        $result = 'success';
    } catch (Exception $e) {
        $result = $e->getMessage();
    }

    expect($result)->toBe('success');
    expect(DB::table('notifications')->count())->toBe(1);
});

it('can create a endpoint arn to send push notifications using aws sns service', function () {
    Helpers::setupAwsEnv();

    Config::set('notifications.aws_sns_application_arn', 'arn:aws:sns:us-east-1:123456789012:app/GCM/XXXXXX');

    $mockResult = Mockery::mock('Aws\Result');
    $mockResult->shouldReceive('get')
        ->once()
        ->with('EndpointArn')
        ->andReturn('arn:aws:sns:us-east-1:123456789012:endpoint/GCM/SomeEndpointArn');

    $snsClientMock = Mockery::mock(SnsClient::class);
    $snsClientMock->shouldReceive('createPlatformEndpoint')
        ->once()
        ->with([
            'PlatformApplicationArn' => 'arn:aws:sns:us-east-1:123456789012:app/GCM/XXXXXX',
            'Token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'CustomUserData' => json_encode(['user_id' => 1]),
        ])
        ->andReturn($mockResult);

    $notifications = Mockery::mock(LaravelNotifications::class)->makePartial();
    $notifications->shouldAllowMockingProtectedMethods();
    $notifications->shouldReceive('getSnsClient')->andReturn($snsClientMock);

    $endpointArn = $notifications->createEndpointArn(
        'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        ['user_id' => 1]
    );

    expect($endpointArn)->toBe('arn:aws:sns:us-east-1:123456789012:endpoint/GCM/SomeEndpointArn');
});

it('should not use a custom user data with more than 256 characters', function () {
    Helpers::setupAwsEnv();

    Config::set('notifications.aws_sns_application_arn', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

    $notifications = new LaravelNotifications;

    expect(function () use ($notifications) {
        $notifications->createEndpointArn(
            'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            ['user_id' => str_repeat('a', 257)]
        );
    })->toThrow(InvalidArgumentException::class);
});

it('should return null when creating a endpoint arn without device token', function () {
    Helpers::setupAwsEnv();

    Config::set('notifications.aws_sns_application_arn', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

    $notifications = new LaravelNotifications;

    expect($notifications->createEndpointArn('', ['user_id' => 1]))->toBeNull();
    expect($notifications->createEndpointArn(null, ['user_id' => 1]))->toBeNull();
});
