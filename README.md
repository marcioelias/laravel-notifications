# Send notifications with AWS SNS made easy.
[![Latest Version on Packagist](https://img.shields.io/packagist/v/marcioelias/laravel-notifications.svg?style=flat-square)](https://packagist.org/packages/marcioelias/laravel-notifications)
![Tests](https://github.com/marcioelias/laravel-notifications/workflows/run-tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/marcioelias/laravel-notifications.svg?style=flat-square)](https://packagist.org/packages/marcioelias/laravel-notifications)

The goal of this package is turn very easy the process of sending notifications using AWS SNS. It provides a facade to send notifications, and an API to be used to interact with the notifications (push notifications only).

Here are the endpoints available:

```php 
# used to create a endpoint ARN on AWS SNS Application
# stores the ARN at users table on column device_token)

POST /api/device-token 
```

```php 
# used to get all notifications paginated
# see config to customize per page number and FormResource class

GET /api/notifications 
```

```php 
# mark a notification as readed

PUT /api/notification/{notification}/read
```

```php 
# mark a notification as unreaded

PUT /api/notification/{notification}/unread
```

## Installation

You can install the package via composer:

```bash
composer require marcioelias/laravel-notifications
```

Publish the migrations:

```bash
php artisan vendor:publish --tag="laravel-notifications-migrations"
```

Publish the config file with (Optional, just required if you need to config some customizations):

```bash
php artisan vendor:publish --tag="laravel-notifications-config"
```

This is the contents of the published config file:

```php
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
    | Tables with notifiable trait on his models
    |--------------------------------------------------------------------------
    |
    | Will be executed a migration to create device_token column on each table
    | listed here.
    |
    */
    'notifiable_tables' => [
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
        'notification_resource' =>  \MarcioElias\LaravelNotifications\Resources\NotificationResource::class,
        'pagination' => 20,
    ]
];
```

After config the package, run the migrations
```bash
php artisan migrate
```

## Usage

Sending a push notification

```php
    use MarcioElias\LaravelNotifications\LaravelNotifications;

    ...

    public function myFunction(): void
    {
        //my own code 
        ...
        LaravelNotifications::sendPush(
            title: 'my title',
            body: 'Hello, this is a push notification'
        )
    }
```

You can add some custom object to the push notification.

```php
    use MarcioElias\LaravelNotifications\LaravelNotifications;

    ...

    public function myFunction(): void
    {
        //my own code 
        ...
        LaravelNotifications::sendPush(
            title: 'my title',
            body: 'Hello, this is a push notification',
            data: [
                'id' => Auth::id(),
                'name' => Auth::user()->name
            ]
        )
    }
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Marcio Elias](https://github.com/marcioelias)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
