<?php

// config for MarcioElias/LaravelNotifications
return [
    /*
    |--------------------------------------------------------------------------
    | Supported Push Notification Providers
    |--------------------------------------------------------------------------
    |
    | Currently this package can send push notification with AWS SNS Service.
    | Please not that using AWS services, this package will be using by default
    | the default configurations for AWS on .env file.
    |
    | Here are the keys that can be used to configure the service provider:
    |  - aws_sns
    |
    */
    'push_service_provider' => env('PUSH_SERVICE_PROVIDER', 'aws_sns'),
];
