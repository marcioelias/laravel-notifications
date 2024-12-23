<?php

namespace MarcioElias\LaravelNotifications;

use Aws\Sns\SnsClient;
use MarcioElias\LaravelNotifications\Enums\NotificationType;
use MarcioElias\LaravelNotifications\Models\Notification;

class LaravelNotifications
{
    public function sendPush(string $to, string $title, ?string $body = null, array $data = [])
    {
        $payload = $this->getSnsPushPayload($title, $body, $data);

        try {
            $pushClient = $this->getPushClient();

            $pushClient->publish([
                'TargetArn' => $to,
                'Message' => json_encode($payload),
                'MessageStructure' => 'json',
            ]);

            $this->storeNotification(
                $title,
                $body,
                NotificationType::PUSH,
                $payload);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function readNotification(Notification $notification)
    {
        $notification->update(['readed' => true]);
    }

    protected function getPushClient()
    {
        return match (config('notifications.push_service_provider')) {
            'aws_sns' => $this->getSnsClient(),
            default => throw new \Exception('Push client not found')
        };
    }

    protected function getSnsClient()
    {
        return new SnsClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    protected function storeNotification(string $title, ?string $body = null, NotificationType $notificationType = NotificationType::SMS, array $data = [])
    {
        Notification::create([
            'title' => $title,
            'body' => $body,
            'notification_type' => $notificationType,
            'data' => $data,
        ]);
    }

    protected function getSnsPushPayload(string $title, ?string $body = null, array $data = [])
    {
        return [
            'default' => $title,
            'GCM'     => json_encode([
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data,
            ]),
            'APNS'    => json_encode([
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'sound' => 'default',
                ],
                'data' => $data,
            ]),
        ];
    }
}
