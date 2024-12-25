<?php

namespace MarcioElias\LaravelNotifications\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MarcioElias\LaravelNotifications\Enums\NotificationType;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'notification_type',
        'readed',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'notification_type' => NotificationType::class,
            'readed' => 'boolean',
            'data' => 'array',
        ];
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
