<?php

namespace MarcioElias\LaravelNotifications;

use MarcioElias\LaravelNotifications\Commands\LaravelNotificationsCommand;
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
            ->hasConfigFile()
            ->hasMigration('create_notifications_table')
            ->hasCommand(LaravelNotificationsCommand::class);
    }
}
