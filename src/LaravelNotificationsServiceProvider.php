<?php

namespace MarcioElias\LaravelNotifications;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MarcioElias\LaravelNotifications\Commands\LaravelNotificationsCommand;

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
            ->hasViews()
            ->hasMigration('create_laravel_notifications_table')
            ->hasCommand(LaravelNotificationsCommand::class);
    }
}
