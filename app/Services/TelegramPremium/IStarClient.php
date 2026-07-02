<?php

namespace App\Services\TelegramPremium;

use App\Services\AppConfigService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IStarClient
{
    public function __construct(
        protected AppConfigService $config,
    ) {}

    public function configured(): bool
    {
        return (string) $this->config->get('ISTAR_API_KEY', '') !== '';
    }

    public function baseUrl(): string
    {
        return rtrim(
            (string) $this->config->get('ISTAR_API_BASE', 'https://v1.fragmentapi.com/api/v1/partner'),
            '/'
        );
    }

    /**
     * @return array<int, array{months: int, usd_value: float, ton_value: float|null}>
     */
    public function premiumPackages(): array
    {
        $response = $this->request('get', '/premium/packages');

        if (!$response->successful()) {
            throw new \RuntimeException($this->extractError($response));
        }

        $data = $response->json();
        if (!is_array($data)) {
            return [];
        }

        $packages = [];
        foreach ($data as $item) {
            if (!is_array($item) || !isset($item['months'])) {
                continue;
            }
            $packages[] = [
                'months' => (int) $item['months'],
                'usd_value' => (float) ($item['usd_value'] ?? 0),
                'ton_value' => isset($item['ton_value']) ? (float) $item['ton_value'] : null,
            ];
        }

        usort($packages, fn ($a, $b) => $a['months'] <=> $b['months']);

        return $packages;
    }

    /**
     * @return array<string, mixed>
     */
    public function searchPremiumRecipient(string $username, int $months): array
    {
        $username = ltrim(trim($username), '@');

        $response = $this->request('get', '/premium/recipient/search', [
            'username' => $username,
            'months' => $months,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException($this->extractError($response));
        }

        $data = $response->json();
        if (!is_array($data) || !($data['success'] ?? false)) {
            throw new \RuntimeException('Recipient not found or invalid for Premium gift.');
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function createPremiumOrder(string $username, string $recipientHash, int $months): array
    {
        $username = ltrim(trim($username), '@');

        $response = $this->request('post', '/orders/premium', [
            'username' => $username,
            'recipient_hash' => $recipientHash,
            'months' => $months,
            'wallet_type' => 'TON',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException($this->extractError($response));
        }

        $data = $response->json();
        if (!is_array($data) || empty($data['order_id'])) {
            throw new \RuntimeException('Provider did not return an order id.');
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    protected function request(string $method, string $path, array $query = []): Response
    {
        if (!$this->configured()) {
            throw new \RuntimeException('iStar API is not configured.');
        }

        $this->respectRateLimit();

        $url = $this->baseUrl().'/'.ltrim($path, '/');
        $pending = Http::timeout(30)
            ->acceptJson()
            ->withHeaders(['API-Key' => (string) $this->config->get('ISTAR_API_KEY')]);

        $response = match (strtolower($method)) {
            'post' => $pending->post($url, $query),
            default => $pending->get($url, $query),
        };

        if (!$response->successful()) {
            Log::warning('iStar API error', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return $response;
    }

    protected function respectRateLimit(): void
    {
        $key = 'istar_api_last_call';
        $last = (float) Cache::get($key, 0);
        $elapsed = microtime(true) - $last;
        if ($elapsed < 1.0) {
            usleep((int) ((1.0 - $elapsed) * 1_000_000));
        }
        Cache::put($key, microtime(true), 5);
    }

    protected function extractError(Response $response): string
    {
        $json = $response->json();
        if (is_array($json)) {
            $message = $json['message'] ?? $json['error'] ?? $json['detail'] ?? null;
            if (is_string($message) && $message !== '') {
                return $message;
            }
        }

        return 'iStar API request failed (HTTP '.$response->status().').';
    }
}
