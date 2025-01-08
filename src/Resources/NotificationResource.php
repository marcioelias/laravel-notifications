<?php

namespace MarcioElias\LaravelNotifications\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'notification_type' => $this->notification_type,
            'readed' => $this->readed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('alertable', [
                'id' => $this->alertable->id,
                'name' => $this->alertable->name,
            ]),
        ];
    }
}
