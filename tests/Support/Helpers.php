<?php

namespace MarcioElias\LaravelNotifications\Tests\Support;

use MarcioElias\LaravelNotifications\Tests\Support\Models\User;

class Helpers
{
    public static function fakeUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public static function setupAwsEnv(): void
    {
        putenv('AWS_DEFAULT_REGION=us-east-1');
        putenv('AWS_ACCESS_KEY_ID=test-access-key');
        putenv('AWS_SECRET_ACCESS_KEY=test-secret-key');
    }
}
