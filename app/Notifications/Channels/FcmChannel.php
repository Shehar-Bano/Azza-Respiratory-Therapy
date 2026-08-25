<?php

namespace App\Notifications\Channels;

use App\Models\AppNotification;
use App\Services\FirebaseService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    public function __construct(private readonly FirebaseService $firebaseService)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $payload = $notification->toFcm($notifiable);
        $token = $notifiable->routeNotificationFor('fcm', $notification);

        // Save Notification in Database for API listing
        $this->createAppNotification($notifiable, $payload);

        // Send Push Notification via Firebase Service if user has fcm_token
        if (!empty($token)) {
            try {
                $this->firebaseService->sendToToken(
                    token: $token,
                    title: (string) ($payload['title'] ?? ''),
                    body: (string) ($payload['message'] ?? ''),
                    data: (array) ($payload['data'] ?? [])
                );
            } catch (\Throwable $e) {
                Log::error('FCM Notification Failed', [
                    'notification' => get_class($notification),
                    'user_id' => $notifiable->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function createAppNotification(object $notifiable, array $payload): void
    {
        if (!isset($notifiable->id) || !isset($payload['title']) || !isset($payload['message'])) {
            return;
        }

        AppNotification::create([
            'user_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'title' => (string) $payload['title'],
            'description' => (string) ($payload['message'] ?? ''),
            'type' => (string) ($payload['type'] ?? 'general_notification'),
            'data' => [
                'title' => (string) $payload['title'],
                'message' => (string) $payload['message'],
                ...((array) ($payload['data'] ?? [])),
            ],
            'read_at' => null,
        ]);
    }
}
