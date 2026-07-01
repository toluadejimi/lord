<?php

namespace App\Services;

use App\Models\User;
use App\Models\Verification;
use App\Models\WebhookResponse;
use Illuminate\Support\Facades\Http;

class WebhookDispatchService
{
    public function dispatchForVerification(Verification $verification): void
    {
        $user = User::find($verification->user_id);
        if (!$user || empty($user->webhook_url)) {
            return;
        }

        $payload = [
            'phone' => $verification->phone,
            'code' => $verification->sms,
            'service' => $verification->service,
            'order_id' => (string) $verification->id,
            'full_sms' => $verification->full_sms ?? $verification->sms,
            'country' => $verification->country,
            'timestamp' => time(),
        ];

        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = $user->api_key
            ? hash_hmac('sha256', $body, $user->api_key)
            : null;

        try {
            $request = Http::timeout(15)
                ->withHeaders(array_filter([
                    'Content-Type' => 'application/json',
                    'X-SMSLORD-Signature' => $signature,
                    'X-SMSLORD-Timestamp' => (string) $payload['timestamp'],
                ]))
                ->withBody($body, 'application/json');

            $response = $request->post($user->webhook_url);
            WebhookResponse::create([
                'order_id' => (string) $verification->id,
                'response_code' => $response->status(),
                'response_body' => $response->body(),
                'url' => $user->webhook_url,
            ]);
        } catch (\Throwable $e) {
            WebhookResponse::create([
                'order_id' => (string) $verification->id,
                'response_code' => 0,
                'response_body' => $e->getMessage(),
                'url' => $user->webhook_url,
            ]);
        }
    }
}
