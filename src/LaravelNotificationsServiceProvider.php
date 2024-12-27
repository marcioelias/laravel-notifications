<?php

namespace MarcioElias\LaravelNotifications;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelNotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-notifications')
            ->hasRoute('api')
            ->hasConfigFile('notifications')
            ->hasMigrations(
                'create_notifications_table',
                'alter_users_table_add_device_token_column'
            );
    }
}
