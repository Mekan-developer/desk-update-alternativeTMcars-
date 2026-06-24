<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly string $fcmToken,
        private readonly string $title,
        private readonly string $body,
        private readonly string $type,
        private readonly ?string $linkType = null,
        private readonly ?int $linkId = null,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $serverKey = config('services.fcm.server_key');

        if (! $serverKey) {
            Log::warning('FCM server key not configured');
            return;
        }

        $payload = [
            'to'           => $this->fcmToken,
            'notification' => [
                'title' => $this->title,
                'body'  => $this->body,
            ],
            'data' => [
                'type'      => $this->type,
                'link_type' => $this->linkType,
                'link_id'   => $this->linkId,
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);

        if (! $response->successful()) {
            Log::error('FCM push failed', ['response' => $response->body(), 'token' => $this->fcmToken]);
            $this->fail('FCM request failed: ' . $response->status());
        }
    }
}
