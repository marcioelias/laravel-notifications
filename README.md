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

## Publishing Resources

To see all available publishable resources, run:

```bash
php artisan vendor:publish --provider="MarcioElias\LaravelNotifications\LaravelNotificationsServiceProvider"
```

Or publish specific resources using tags:

**Publish the migrations:**

```bash
php artisan vendor:publish --tag="laravel-notifications-migrations"
```

**Publish the config file** (Optional, just required if you need to config some customizations):

```bash
php artisan vendor:publish --tag="laravel-notifications-config"
```

**Publish translations:**

```bash
php artisan vendor:publish --tag="laravel-notifications-translations"
```

**Publish routes:**

```bash
php artisan vendor:publish --tag="laravel-notifications-routes"
```

**Publish custom fields migration:**

```bash
php artisan vendor:publish --tag="laravel-notifications-custom-fields-migration"
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

### Custom Fields in Notifications Table

You can add custom fields to the `notifications` table to store additional data with each notification.

**Step 1: Publish the custom fields migration**

```bash
php artisan vendor:publish --tag="laravel-notifications-custom-fields-migration"
```

The migration will be published to:
- **Default**: `database/migrations/`
- **Tenancy for Laravel**: Automatically detected and published to `database/tenant` or `Database/Tenant`
- **Custom path**: Configure in `config/notifications.php` or via `NOTIFICATIONS_MIGRATIONS_PATH` env variable

**Configuring custom migrations directory:**

If you're using Tenancy for Laravel, the package will automatically detect it. You can also manually configure the path:

**Option 1: Via config file** (`config/notifications.php`):
```php
'migrations_path' => 'database/tenant', // or 'Database/Tenant'
```

**Option 2: Via environment variable** (`.env`):
```env
NOTIFICATIONS_MIGRATIONS_PATH=database/tenant
```

**Step 2: Edit the migration file**

Open the published migration file and add your custom fields:

```php
public function up()
{
    Schema::table('notifications', function (Blueprint $table) {
        $table->string('category')->nullable();
        $table->integer('priority')->default(0);
        $table->foreignId('related_id')->nullable();
        // Add your custom fields here
    });
}
```

**Step 3: Run the migration**

```bash
php artisan migrate
```

**Step 4: Use custom fields when sending notifications**

```php
use MarcioElias\LaravelNotifications\LaravelNotifications;

LaravelNotifications::sendPush(
    to: $user,
    title: 'New Order',
    body: 'You have a new order',
    data: ['order_id' => 123],
    customFields: [
        'category' => 'order',
        'priority' => 5,
        'related_id' => 123
    ]
);
```

The custom fields will be saved in the `notifications` table along with the standard notification data.

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
