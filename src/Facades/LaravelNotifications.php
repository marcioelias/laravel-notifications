<?php

namespace MarcioElias\LaravelNotifications\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MarcioElias\LaravelNotifications\LaravelNotifications
 */
class LaravelNotifications extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MarcioElias\LaravelNotifications\LaravelNotifications::class;
    }
}
