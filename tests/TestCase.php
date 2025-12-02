<?php

namespace MarcioElias\LaravelNotifications\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Sanctum\SanctumServiceProvider;
use MarcioElias\LaravelNotifications\LaravelNotificationsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MarcioElias\\LaravelNotifications\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelNotificationsServiceProvider::class,
            SanctumServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('auth.defaults.guard', 'sanctum');
        config()->set('sanctum.middleware', [
            'api' => [
                EnsureFrontendRequestsAreStateful::class,
            ],
        ]);

        app()->bind('App\Models\User', 'MarcioElias\LaravelNotifications\Tests\Support\Models\User');

        $testMigration = include __DIR__.'/Support/database/migrations/0001_01_01_000000_create_users_table.php';
        $testMigration->up();

        $migration = include __DIR__.'/../database/migrations/create_notifications_table.php.stub';
        $migration->up();

        // Adiciona campos personalizados para os testes
        $customFieldsMigration = include __DIR__.'/Support/database/migrations/0002_01_01_000000_add_custom_fields_to_notifications_table.php';
        $customFieldsMigration->up();
    }
}
