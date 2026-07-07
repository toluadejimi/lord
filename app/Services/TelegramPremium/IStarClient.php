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

        $data = $this->unwrapList($response->json());

        $packages = [];
        foreach ($data as $item) {
            if (!is_array($item) || !isset($item['months'])) {
                continue;
            }
            $packages[] = [
                'months' => (int) $item['months'],
                'usd_value' => (float) ($item['usd_value'] ?? $item['usd'] ?? $item['amount'] ?? 0),
                'ton_value' => isset($item['ton_value']) ? (float) $item['ton_value'] : null,
            ];
        }

        usort($packages, fn ($a, $b) => $a['months'] <=> $b['months']);

        return $packages;
    }

    /**
     * @return array<string, mixed>
     */
    public function walletBalance(): array
    {
        $response = $this->request('get', '/wallet/balance');

        if (!$response->successful()) {
            throw new \RuntimeException($this->extractError($response));
        }

        $data = $response->json();
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid wallet balance response from iStar.');
        }

        return $data;
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
        if (!is_array($data) || !($data['success'] ?? true)) {
            throw new \RuntimeException('Recipient not found or invalid for Premium gift.');
        }

        $hash = (string) ($data['recipient_hash'] ?? $data['recipient'] ?? '');
        if ($hash === '') {
            throw new \RuntimeException('Recipient could not be verified. Try another username.');
        }

        $data['recipient_hash'] = $hash;
        $data['recipient'] = $hash;

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
            foreach (['message', 'error', 'detail', 'msg'] as $key) {
                $message = $json[$key] ?? null;
                if (is_string($message) && $message !== '') {
                    return $message;
                }
            }

            if (isset($json['errors']) && is_array($json['errors'])) {
                $first = collect($json['errors'])->flatten()->first();
                if (is_string($first) && $first !== '') {
                    return $first;
                }
            }
        }

        $body = trim($response->body());
        if ($body !== '' && strlen($body) < 300) {
            return $body;
        }

        return 'iStar API request failed (HTTP '.$response->status().').';
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function unwrapList(mixed $data): array
    {
        if (!is_array($data)) {
            return [];
        }

        if (array_is_list($data)) {
            return $data;
        }

        foreach (['data', 'packages', 'items', 'results'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                return array_is_list($data[$key]) ? $data[$key] : [];
            }
        }

        return [];
    }
}
