<?php

namespace App\Services\Sms;

use App\Services\AppConfigService;
use Illuminate\Support\Facades\Http;

class HeroHandlerProvider
{
    public function __construct(protected AppConfigService $config) {}

    protected function baseUrl(string $provider): string
    {
        return match ($provider) {
            'hero' => rtrim($this->config->get('SMS_SERVER_HERO_BASE_URL', 'https://hero-sms.com'), '/'),
            'sv3' => rtrim($this->config->get('SMS_SERVER_WORLD_SV3_BASE_URL', 'https://smsbower.page'), '/'),
            'usa1' => 'https://daisysms.com',
            default => 'https://hero-sms.com',
        };
    }

    protected function apiKey(string $provider): string
    {
        return match ($provider) {
            'hero' => (string) $this->config->get('SMS_SERVER_HERO_API_KEY', ''),
            'sv3' => (string) $this->config->get('SMS_SERVER_WORLD_SV3_API_KEY', ''),
            'usa1' => (string) $this->config->get('KEY', ''),
            default => '',
        };
    }

    public function request(string $provider, string $action, array $params = []): string
    {
        $query = array_merge([
            'api_key' => $this->apiKey($provider),
            'action' => $action,
        ], $params);

        $response = Http::timeout(30)->get($this->baseUrl($provider).'/stubs/handler_api.php', $query);

        return (string) $response->body();
    }

    public function getNumber(string $provider, string $service, ?string $country = null, ?string $maxPrice = null): array
    {
        $params = ['service' => $service];
        if ($country) {
            $params['country'] = $country;
        }
        if ($maxPrice) {
            $params['maxPrice'] = $maxPrice;
        }

        $body = $this->request($provider, 'getNumber', $params);

        if (str_starts_with($body, 'ACCESS_NUMBER:')) {
            $parts = explode(':', $body);
            return [
                'success' => true,
                'order_id' => $parts[1] ?? null,
                'phone' => $parts[2] ?? null,
                'raw' => $body,
            ];
        }

        return ['success' => false, 'error' => trim($body), 'raw' => $body];
    }

    public function getStatus(string $provider, string $orderId): array
    {
        $body = $this->request($provider, 'getStatus', ['id' => $orderId]);

        if (str_contains($body, 'STATUS_OK:')) {
            $code = explode(':', $body, 2)[1] ?? '';

            return ['status' => 'ok', 'code' => trim($code), 'full_sms' => trim($code), 'raw' => $body];
        }

        if (str_contains($body, 'STATUS_WAIT_CODE')) {
            return ['status' => 'waiting', 'raw' => $body];
        }

        if (str_contains($body, 'STATUS_CANCEL')) {
            return ['status' => 'cancelled', 'raw' => $body];
        }

        return ['status' => 'unknown', 'raw' => $body];
    }

    public function cancel(string $provider, string $orderId): bool
    {
        $body = $this->request($provider, 'setStatus', ['id' => $orderId, 'status' => 8]);

        return str_contains($body, 'ACCESS_CANCEL');
    }

    public function getPrices(string $provider, ?string $service = null): string
    {
        $params = [];
        if ($service) {
            $params['service'] = $service;
        }

        return $this->request($provider, 'getPrices', $params);
    }

    public function getCountries(string $provider): string
    {
        return $this->request($provider, 'getCountries');
    }

    public function getServicesList(string $provider): string
    {
        return $this->request($provider, 'getServicesList');
    }
}
