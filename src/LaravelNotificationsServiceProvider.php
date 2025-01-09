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
            ->hasTranslations()
            ->hasMigrations(
                'create_notifications_table',
                'alter_users_table_add_device_token_column',
                'alter_users_table_add_endpoint_arn_column'
            );
    }

    public function bootingPackage()
    {
        $this->publishes([
            $this->package->basePath('/../routes/') => base_path("routes/vendor/{$this->package->shortName()}"),
        ], "{$this->package->shortName()}-routes");
    }
}
