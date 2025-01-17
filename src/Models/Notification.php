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

    public function markAsRead(): void
    {
        $this->update(['readed' => true]);
    }

    public function markAsUnread(): void
    {
        $this->update(['readed' => false]);
    }

    public function alertable()
    {
        return $this->morphTo();
    }
}
