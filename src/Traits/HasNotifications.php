<?php

namespace MarcioElias\LaravelNotifications\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use MarcioElias\LaravelNotifications\Models\Notification;

trait HasNotifications
{
    public function alertable(): MorphMany
    {
        return $this->morphMany(Notification::class, 'alertable');
    }

    public function getDestination(): ?string
    {
        return match (config('notifications.push_service_provider')) {
            'aws_sns' => $this->endpoint_arn,
            default => $this->device_token
        };
    }
}
