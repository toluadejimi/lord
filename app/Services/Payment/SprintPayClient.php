<?php

namespace App\Services\Payment;

use App\Services\AppConfigService;
use Illuminate\Support\Facades\Http;

class SprintPayClient
{
    public function __construct(protected AppConfigService $config) {}

    protected function base(): string
    {
        return rtrim($this->config->get('SPRINTPAY_API_BASE', 'https://web.sprintpay.online/api'), '/');
    }

    public function payUrl(float $amount, string $ref, string $email): string
    {
        $key = $this->config->get('WEBKEY', '');

        return 'https://web.sprintpay.online/pay?'.http_build_query([
            'amount' => $amount,
            'key' => $key,
            'ref' => $ref,
            'email' => $email,
        ]);
    }

    public function resolve(string $sessionId, string $ref): array
    {
        $response = Http::asForm()->post($this->base().'/resolve', [
            'session_id' => $sessionId,
            'ref' => $ref,
        ]);

        return $response->json() ?? [];
    }

    public function resolveComplete(string $orderId): bool
    {
        $response = Http::asForm()->post($this->base().'/resolve-complete', [
            'order_id' => $orderId,
        ]);
        $data = $response->json();

        return ($data['status'] ?? false) === true;
    }

    public function generateVirtualAccount(string $email, ?string $name = null): array
    {
        $response = Http::asForm()->post($this->base().'/generate-virtual-account', array_filter([
            'key' => $this->config->get('WEBKEY', ''),
            'email' => $email,
            'name' => $name,
        ]));

        return $response->json() ?? [];
    }
}
