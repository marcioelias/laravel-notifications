# Send notifications with AWS SNS made easy.

The goal of this package is turn very easy the process of sending notifications using AWS SNS. 

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-notifications.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-notifications)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require marcioelias/laravel-notifications
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="laravel-notifications-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-notifications-config"
```

This is the contents of the published config file:

```php
return [
    'push_service_provider' => env('PUSH_SERVICE_PROVIDER', 'aws_sns'),
];
```

## Usage

```php
    // will be available soon
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
