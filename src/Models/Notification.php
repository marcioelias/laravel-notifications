<?php

namespace MarcioElias\LaravelNotifications\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MarcioElias\LaravelNotifications\Enums\NotificationType;

class Notification extends Model
{
    use HasFactory;

    /**
     * Campos protegidos que não podem ser preenchidos em massa.
     * Campos personalizados adicionados pelo usuário serão permitidos dinamicamente.
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

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

    /**
     * Permite adicionar campos personalizados dinamicamente
     * Remove campos protegidos antes de permitir mass assignment
     */
    public function fillCustomFields(array $attributes): self
    {
        $protectedFields = ['id', 'title', 'body', 'notification_type', 'readed', 'data', 'alertable_id', 'alertable_type', 'created_at', 'updated_at'];

        $customFields = array_diff_key($attributes, array_flip($protectedFields));

        foreach ($customFields as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }
}
