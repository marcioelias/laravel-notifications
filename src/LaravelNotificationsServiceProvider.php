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

        // Publica a migration stub para o usuário adicionar campos personalizados
        $migrationPath = $this->package->basePath('/../database/migrations/add_custom_fields_to_notifications_table.php.stub');
        
        // Garante que o arquivo existe antes de registrar (necessário para o Laravel registrar a tag)
        if (file_exists($migrationPath)) {
            $this->publishes([
                realpath($migrationPath) => database_path('migrations/0000_00_00_000000_add_custom_fields_to_notifications_table.php'),
            ], "{$this->package->shortName()}-custom-fields-migration");
        }
    }
}
