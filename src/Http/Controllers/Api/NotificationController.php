<?php

namespace MarcioElias\LaravelNotifications\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MarcioElias\LaravelNotifications\Models\Notification;
use MarcioElias\LaravelNotifications\Resources\NotificationResource;

class NotificationController
{
    public function index(Request $request)
    {
        $notifications = Auth::user()->notifiable()
            ->when($request->has('readed'), fn ($query) => $query->where('readed', $request->readed))
            ->orderBy('created_at', 'desc')
            ->simplePaginate(config('notifications.api.pagination'));

        return NotificationResource::collection($notifications);
    }

    public function update(Request $request, Notification $notification)
    {
        // dd($request->all());
        $notification->update(['readed' => $request->readed]);
        return response()->json(['message' => 'Notification read status updated'], 202);
    }
}