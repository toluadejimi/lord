<?php

namespace App\Services\Sms;

use App\Services\AppConfigService;
use Illuminate\Support\Facades\Http;

class SmsPoolProvider
{
    public function __construct(protected AppConfigService $config) {}

    protected function key(): string
    {
        return (string) $this->config->get('WKEY', '');
    }

    public function countries(): ?array
    {
        $response = Http::timeout(30)->post('https://api.smspool.net/country/retrieve_all', ['key' => $this->key()]);

        return $response->json();
    }

    public function services(): ?array
    {
        $response = Http::timeout(30)->post('https://api.smspool.net/service/retrieve_all', ['key' => $this->key()]);

        return $response->json();
    }

    public function price(string $country, string $service): ?object
    {
        $response = Http::timeout(30)->post('https://api.smspool.net/request/price', [
            'key' => $this->key(),
            'country' => $country,
            'service' => $service,
            'pool' => '',
        ]);

        return $response->object();
    }

    public function purchase(string $country, string $service): ?object
    {
        $response = Http::timeout(30)->post('https://api.smspool.net/purchase/sms', [
            'key' => $this->key(),
            'country' => $country,
            'service' => $service,
        ]);

        return $response->object();
    }

    public function checkSms(string $orderId): ?object
    {
        $response = Http::timeout(30)->post('https://api.smspool.net/sms/check', [
            'key' => $this->key(),
            'orderid' => $orderId,
        ]);

        return $response->object();
    }

    public function cancel(string $orderId): ?object
    {
        $response = Http::timeout(30)->post('https://api.smspool.net/sms/cancel', [
            'key' => $this->key(),
            'orderid' => $orderId,
        ]);

        return $response->object();
    }
}
