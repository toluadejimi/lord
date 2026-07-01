<?php

namespace App\Services\Payment;

use App\Services\AppConfigService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class SprintPayVasClient
{
    public function __construct(protected AppConfigService $config) {}

    public function configured(): bool
    {
        return $this->webkey() !== '' && $this->bearerSecret() !== '';
    }

    public function baseUrl(): string
    {
        return rtrim($this->config->get('SPRINTPAY_API_BASE', 'https://web.sprintpay.online/api'), '/');
    }

    public function webkey(): string
    {
        return (string) $this->config->get('WEBKEY', '');
    }

    public function bearerSecret(): string
    {
        return (string) $this->config->get('SPRINTPAY_WEBHOOK_SECRET', '');
    }

    public function getPublic(string $path, array $query = []): Response
    {
        return Http::timeout(60)
            ->get($this->baseUrl().'/'.ltrim($path, '/'), $query);
    }

    public function postMerchantVas(string $path, array $body = []): Response
    {
        $body['key'] = $this->webkey();

        return Http::timeout(60)
            ->withToken($this->bearerSecret())
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl().'/'.ltrim($path, '/'), $body);
    }

    public function getMerchantVas(string $path, array $query = []): Response
    {
        $query['key'] = $this->webkey();

        return Http::timeout(60)
            ->withToken($this->bearerSecret())
            ->acceptJson()
            ->get($this->baseUrl().'/'.ltrim($path, '/'), $query);
    }

    public function responseIndicatesSuccess(Response $response): bool
    {
        if (!$response->successful()) {
            return false;
        }

        $json = $response->json();

        if (!is_array($json)) {
            return true;
        }

        if (array_key_exists('status', $json)) {
            $status = $json['status'];
            if ($status === true || $status === 1) {
                return true;
            }
            if (is_string($status)) {
                $normalized = strtolower($status);
                if (in_array($normalized, ['success', '1', '200', 'true'], true)) {
                    return true;
                }
            }
        }

        if (($json['success'] ?? null) === true) {
            return true;
        }

        $responseCode = (string) ($json['response_code'] ?? $json['code'] ?? '');
        if (in_array($responseCode, ['000', '00'], true)) {
            return true;
        }

        return false;
    }

    public function extractMessage(Response $response): string
    {
        $json = $response->json();

        if (is_array($json)) {
            foreach (['message', 'msg', 'error', 'description'] as $key) {
                if (!empty($json[$key]) && is_string($json[$key])) {
                    return $json[$key];
                }
                if (isset($json['data'][$key]) && is_string($json['data'][$key])) {
                    return $json['data'][$key];
                }
            }
        }

        $body = trim($response->body());

        return $body !== '' ? mb_substr($body, 0, 240) : 'Provider request failed.';
    }

    public function extractElectricityToken(Response $response): ?string
    {
        $json = $response->json();

        if (!is_array($json)) {
            return null;
        }

        foreach (['token', 'purchased_code'] as $key) {
            if (!empty($json[$key]) && is_string($json[$key])) {
                return $json[$key];
            }
            if (!empty($json['data'][$key]) && is_string($json['data'][$key])) {
                return $json['data'][$key];
            }
        }

        return null;
    }

    /** @deprecated Use getPublic('/get-service') — kept for admin category fetch compatibility */
    public function categories(): array
    {
        $response = $this->getPublic('/get-service');

        return $response->json() ?? [];
    }
}
