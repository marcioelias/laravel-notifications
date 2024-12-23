<?php

use MarcioElias\LaravelNotifications\Facades\LaravelNotifications;
use MarcioElias\LaravelNotifications\LaravelNotifications as LaravelNotificationsClass;

it('resolves the correct class for the facade', function () {
    $resolvedClass = LaravelNotifications::getFacadeRoot();

    expect($resolvedClass)->toBeInstanceOf(LaravelNotificationsClass::class);
});

it('allows calling methods on the facade', function () {
    $mockedService = Mockery::mock(LaravelNotificationsClass::class);
    $mockedService->shouldReceive('sendPush')
        ->once()
        ->with('test-arn', 'title', 'body', ['message' => 'Hello World'])
        ->andReturn(true);

    $this->app->instance(LaravelNotificationsClass::class, $mockedService);

    $result = LaravelNotifications::sendPush('test-arn', 'title', 'body', ['message' => 'Hello World']);

    expect($result)->toBeTrue();
});

