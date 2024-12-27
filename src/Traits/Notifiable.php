<?php

namespace MarcioElias\LaravelNotifications\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use MarcioElias\LaravelNotifications\Models\Notification;

trait Notifiable
{
    public function notifiable(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
