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

        $response = Http::timeout(45)
            ->retry(2, 500, throw: false)
            ->get($this->baseUrl($provider).'/stubs/handler_api.php', $query);

        if (!$response->successful()) {
            return 'BAD_RESPONSE:'.$response->status();
        }

        return (string) $response->body();
    }

    public function getNumber(
        string $provider,
        string $service,
        ?string $country = null,
        ?string $maxPrice = null,
        array $extra = [],
    ): array {
        $params = array_merge(['service' => $service], $extra);

        if ($country) {
            $params['country'] = $country;
        }
        if ($maxPrice) {
            $params['maxPrice'] = $maxPrice;
        }

        $body = trim($this->request($provider, 'getNumber', $params));

        if (str_starts_with($body, 'ACCESS_NUMBER:')) {
            $parts = explode(':', $body);
            return [
                'success' => true,
                'order_id' => $parts[1] ?? null,
                'phone' => $parts[2] ?? null,
                'raw' => $body,
            ];
        }

        return ['success' => false, 'error' => $this->humanizeError($body), 'raw' => $body];
    }

    protected function humanizeError(string $body): string
    {
        $map = [
            'NO_NUMBERS' => 'No numbers available for this country and service.',
            'NO_BALANCE' => 'Provider balance is low. Try again later.',
            'BAD_SERVICE' => 'Unknown service code.',
            'BAD_COUNTRY' => 'Unknown country.',
            'BAD_KEY' => 'Provider API key is invalid.',
            'ERROR_SQL' => 'Provider is temporarily unavailable.',
        ];

        foreach ($map as $code => $message) {
            if (str_contains($body, $code)) {
                return $message;
            }
        }

        return $body !== '' ? $body : 'Could not rent number.';
    }

    public function getStatus(string $provider, string $orderId): array
    {
        $body = trim($this->request($provider, 'getStatus', ['id' => $orderId]));

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

    public function getPrices(string $provider, ?string $service = null, ?string $country = null, string $action = 'getPrices'): string
    {
        $params = [];

        if ($service) {
            $params['service'] = $service;
        }

        if ($country !== null && $country !== '') {
            $params['country'] = $country;
        }

        return $this->request($provider, $action, $params);
    }

    public function getCountries(string $provider): string
    {
        return $this->request($provider, 'getCountries');
    }

    public function getServicesList(string $provider): string
    {
        if ($this->apiKey($provider) === '') {
            return 'BAD_KEY';
        }

        $body = $this->request($provider, 'getServicesList');

        if ($this->looksLikeServicesJson($body)) {
            return $body;
        }

        if ($provider === 'sv3') {
            $fallback = $this->request($provider, 'getServices');
            if ($this->looksLikeServicesJson($fallback)) {
                return $fallback;
            }
        }

        return $body;
    }

    protected function looksLikeServicesJson(string $body): bool
    {
        $body = trim($body);

        if ($body === '' || $this->isProviderErrorText($body)) {
            return false;
        }

        if (!str_starts_with($body, '{') && !str_starts_with($body, '[')) {
            return false;
        }

        $json = json_decode($body, true);

        if (!is_array($json)) {
            return false;
        }

        if (isset($json['services']) && is_array($json['services']) && $json['services'] !== []) {
            return true;
        }

        return array_is_list($json) && $json !== [];
    }

    protected function isProviderErrorText(string $raw): bool
    {
        $errors = ['BAD_KEY', 'BAD_ACTION', 'ERROR_SQL', 'BAD_RESPONSE:', 'NO_BALANCE'];

        foreach ($errors as $error) {
            if (str_contains($raw, $error)) {
                return true;
            }
        }

        return false;
    }
}
