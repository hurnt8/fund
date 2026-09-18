<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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

    public function sendToUser(User $user, string $title, string $body, string $url = '/app/notifications', string $tag = 'aurenza'): void
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
                'endpoint' => $sub->endpoint,
                'keys'     => [
                    'p256dh' => $this->toBase64Url($sub->public_key),
                    'auth'   => $this->toBase64Url($sub->auth_token),
                ],
            ]);
            $this->webPush->queueNotification($subscription, $payload);
        }

        foreach ($this->webPush->flush() as $report) {
            if ($report->isSuccess()) {
                Log::info('[Push] Sent OK to user ' . $user->id . ' — ' . $report->getEndpoint());
            } else {
                $reason = $report->getReason();
                $status = $report->getResponse()?->getStatusCode();
                Log::error('[Push] Failed for user ' . $user->id
                    . ' endpoint=' . substr($report->getEndpoint(), 0, 80)
                    . ' status=' . $status
                    . ' reason=' . $reason);

                // Supprimer les souscriptions expirées (410 Gone) ou invalides (404)
                if (in_array($status, [404, 410], true)) {
                    $stale[] = $report->getEndpoint();
                }
            }
        }

        if (!empty($stale)) {
            PushSubscription::whereIn('endpoint', $stale)->delete();
            Log::info('[Push] Removed ' . count($stale) . ' stale subscription(s) for user ' . $user->id);
        }
    }

    private function toBase64Url(string $key): string
    {
        $decoded = base64_decode(strtr($key, '-_', '+/'), true);
        if ($decoded === false) {
            return $key;
        }
        return rtrim(strtr(base64_encode($decoded), '+/', '-_'), '=');
    }
}
