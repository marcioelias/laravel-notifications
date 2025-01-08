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

    /*
    |--------------------------------------------------------------------------
    | AWS SNS Application ARN
    |--------------------------------------------------------------------------
    |
    | The ARN of the application that will be used to send push notifications.
    | This ARN is used to create a platform endpoint.
    |
    */
    'aws_sns_application_arn' => env('AWS_SNS_APPLICATION_ARN', null),

    /*
    |--------------------------------------------------------------------------
    | Tables with alertable trait on his models
    |--------------------------------------------------------------------------
    |
    | Will be executed a migration to create device_token column on each table
    | listed here.
    |
    */
    'alertable_tables' => [
        'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Endpoints configuration
    |--------------------------------------------------------------------------
    |
    | Allow the configuration of the API endpoints for the notifications
    | resources.
    |
    */
    'api' => [
        'notification_resource' => \MarcioElias\LaravelNotifications\Resources\NotificationResource::class,
        'pagination' => 20,
    ],

];
