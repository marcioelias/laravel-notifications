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

        // Publica a migration para campos personalizados
        $migrationPath = $this->package->basePath('/../database/migrations/add_custom_fields_to_notifications_table.php.stub');

        if (file_exists($migrationPath)) {
            // Usa uma closure que será executada quando a publicação acontecer
            // Isso permite calcular o caminho dinamicamente baseado na configuração
            $this->publishes([
                $migrationPath => function () {
                    $destinationPath = $this->getMigrationsDestinationPath();

                    return $destinationPath.'/'.date('Y_m_d_His').'_add_custom_fields_to_notifications_table.php';
                },
            ], "{$this->package->shortName()}-custom-fields-migration");
        }
    }

    /**
     * Get the destination path for migrations.
     * Detects Tenancy for Laravel automatically or uses config.
     */
    protected function getMigrationsDestinationPath(): string
    {
        // Check if custom path is configured
        $customPath = config('notifications.migrations_path');
        if ($customPath) {
            return base_path($customPath);
        }

        // Auto-detect Tenancy for Laravel
        if ($this->isTenancyInstalled()) {
            // Check common Tenancy paths
            $tenancyPaths = [
                'database/tenant',
                'Database/Tenant',
                'database/tenants',
                'Database/Tenants',
            ];

            foreach ($tenancyPaths as $path) {
                $fullPath = base_path($path);
                if (is_dir($fullPath)) {
                    return $fullPath;
                }
            }

            // Default Tenancy path if directory doesn't exist yet
            return base_path('database/tenant');
        }

        // Default Laravel migrations path
        return database_path('migrations');
    }

    /**
     * Check if Tenancy for Laravel package is installed.
     */
    protected function isTenancyInstalled(): bool
    {
        return class_exists('Stancl\Tenancy\TenancyServiceProvider')
            || class_exists('Stancl\Tenancy\Tenant')
            || is_dir(base_path('database/tenant'))
            || is_dir(base_path('Database/Tenant'));
    }
}
