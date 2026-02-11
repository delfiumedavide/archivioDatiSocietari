<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    private ?WebPush $webPush = null;

    private function getWebPush(): WebPush
    {
        if ($this->webPush === null) {
            $auth = [
                'VAPID' => [
                    'subject' => config('archivio.vapid.subject', env('VAPID_SUBJECT')),
                    'publicKey' => env('VAPID_PUBLIC_KEY'),
                    'privateKey' => env('VAPID_PRIVATE_KEY'),
                ],
            ];

            $this->webPush = new WebPush($auth);
        }

        return $this->webPush;
    }

    public function sendToUser(User $user, string $title, string $body, ?string $url = null): void
    {
        $subscriptions = $user->pushSubscriptions;

        if ($subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => '/images/logo-icon.svg',
            'url' => $url ?? '/',
        ]);

        $webPush = $this->getWebPush();

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
                'contentEncoding' => $sub->content_encoding ?? 'aesgcm',
            ]);

            $webPush->queueNotification($subscription, $payload);
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isExpired()) {
                PushSubscription::where('endpoint', $report->getEndpoint())->delete();
            }
        }
    }

    public function sendToAdmins(string $title, string $body, ?string $url = null): void
    {
        $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();

        foreach ($admins as $admin) {
            $this->sendToUser($admin, $title, $body, $url);
        }
    }
}
