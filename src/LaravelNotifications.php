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
        $payloadData = ! empty($data) ? ['data' => $data] : [];

        return [
            'GCM' => json_encode(array_merge([
                'fcmV1Message' => [
                    'message' => [
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ],
                ],
            ], $payloadData)),

            'APNS' => json_encode(array_merge([
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'sound' => 'default',
                ],
            ], $payloadData)),

            'APNS_SANDBOX' => json_encode(array_merge([
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'sound' => 'default',
                ],
            ], $payloadData)),

            'ADM' => json_encode(array_merge([
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ], $payloadData)),
        ];
    }
}
