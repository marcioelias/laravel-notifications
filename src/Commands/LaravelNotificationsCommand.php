<?php

namespace MarcioElias\LaravelNotifications\Commands;

use Illuminate\Console\Command;

class LaravelNotificationsCommand extends Command
{
    public $signature = 'laravel-notifications';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
