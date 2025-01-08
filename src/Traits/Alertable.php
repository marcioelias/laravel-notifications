<?php

namespace MarcioElias\LaravelNotifications\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use MarcioElias\LaravelNotifications\Models\Notification;

trait Alertable
{
    public function alertable(): MorphMany
    {
        return $this->morphMany(Notification::class, 'alertable');
    }
}
