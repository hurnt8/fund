<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushService
{
    private WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('services.vapid.subject'),
                'publicKey'  => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ]);
        $this->webPush->setReuseVAPIDHeaders(true);
    }

    public function sendToUser(User $user, string $title, string $body, string $url = '/app/notifications', string $tag = 'credixa'): void
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'url'   => $url,
            'tag'   => $tag,
        ]);

        $stale = [];

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint'        => $sub->endpoint,
                'keys'            => [
                    'p256dh' => $this->toBase64Url($sub->public_key),
                    'auth'   => $this->toBase64Url($sub->auth_token),
                ],
            ]);
            $this->webPush->queueNotification($subscription, $payload);
        }

        foreach ($this->webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                // Endpoint expired or invalid — remove stale subscription
                $stale[] = $report->getRequest()->getUri()->__toString();
            }
        }

        if (!empty($stale)) {
            PushSubscription::whereIn('endpoint', $stale)->delete();
        }
    }

    private function toBase64Url(string $key): string
    {
        // Accepte base64 standard ou base64url, retourne toujours base64url sans padding
        $decoded = base64_decode(strtr($key, '-_', '+/'), true);
        if ($decoded === false) {
            return $key; // Déjà dans un format inconnu, passer tel quel
        }
        return rtrim(strtr(base64_encode($decoded), '+/', '-_'), '=');
    }
}
