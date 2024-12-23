<?php

namespace MarcioElias\LaravelNotifications\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MarcioElias\LaravelNotifications\Enums\NotificationType;
use MarcioElias\LaravelNotifications\Models\Notification;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'notification_type' => $this->faker->randomElement(NotificationType::cases())->value,
            'icon' => $this->faker->imageUrl(200, 200),
            'sound' => 'default',
            'readed' => false,
            'data' => json_encode([]),
        ];
    }
}
