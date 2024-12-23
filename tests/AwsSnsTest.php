<?php

use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use MarcioElias\LaravelNotifications\LaravelNotifications;

it('throws an exception if push client is not found', function () {
    Config::set('notifications.push_service_provider', 'unknown_provider');

    $notifications = new LaravelNotifications;

    $notifications->sendPush(
        'arn:aws:sns:us-east-1:123456789012:endpoint/APNS/MyApp/fd6dc79a-cf27-42a3-8c61-d56be70bb43d',
        'title',
        'body',
        ['alert' => 'Hello, world!']
    );
})->throws(Exception::class, 'Push client not found');

it('returns a valid SnsClient instance using reflection', function () {
    putenv('AWS_DEFAULT_REGION=us-east-1');
    putenv('AWS_ACCESS_KEY_ID=test-access-key');
    putenv('AWS_SECRET_ACCESS_KEY=test-secret-key');

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

    // Ensure the mock SNS client is called with the exact message structure
    $snsClientMock->shouldReceive('publish')
        ->once()
        ->with([
            'TargetArn' => 'arn:aws:sns:us-east-1:123456789012:endpoint/APNS/MyApp/fd6dc79a-cf27-42a3-8c61-d56be70bb43d',
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
            'arn:aws:sns:us-east-1:123456789012:endpoint/APNS/MyApp/fd6dc79a-cf27-42a3-8c61-d56be70bb43d',
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
