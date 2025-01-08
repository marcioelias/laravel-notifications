<?php

namespace MarcioElias\LaravelNotifications\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MarcioElias\LaravelNotifications\Models\Notification;
use MarcioElias\LaravelNotifications\Resources\CountUnreadNotificationResource;
use MarcioElias\LaravelNotifications\Resources\NotificationResource;

class NotificationController
{
    public function index(Request $request)
    {
        $notifications = Auth::user()->alertable()
            ->when($request->has('readed'), fn ($query) => $query->where('readed', $request->readed))
            ->orderBy('created_at', 'desc')
            ->simplePaginate(config('notifications.api.pagination'));

        return config('notifications.api.notification_resource')
            ? config('notifications.api.notification_resource')::collection($notifications)
            : NotificationResource::collection($notifications);
    }

    public function markAllAsRead(Request $request)
    {
        Auth::user()->alertable()->update(['readed' => true]);

        return response()->json(['message' => 'You’ve marked all notifications as read!'], 202);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        $notification->markAsRead();

        return response()->json(['message' => 'You’ve marked the notification as read!'], 202);
    }

    public function markAsUnread(Request $request, Notification $notification)
    {
        $notification->markAsUnread();

        return response()->json(['message' => 'You’ve marked the notification as unread!'], 202);
    }

    public function unreaded()
    {
        return CountUnreadNotificationResource::make(
            Auth::user()
                ->alertable()
                ->where('readed', false)
                ->count()
        );
    }
}
