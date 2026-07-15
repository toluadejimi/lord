<?php

namespace App\Services\Esim;

use App\Services\AppConfigService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PikaSIM reseller API client.
 *
 * @see https://pikasim.com/api/v1/reseller
 */
class PikaSimClient
{
    public function __construct(
        protected AppConfigService $config,
    ) {}

    public function configured(): bool
    {
        return (string) $this->config->get('PIKASIM_API_KEY', '') !== '';
    }

    public function enabled(): bool
    {
        return $this->config->getBool('provider_pikasim_enabled', false)
            && $this->configured();
    }

    public function baseUrl(): string
    {
        return rtrim(
            (string) $this->config->get('PIKASIM_API_BASE', 'https://pikasim.com/api/v1/reseller'),
            '/'
        );
    }

    public function webhookSecret(): string
    {
        return (string) $this->config->get('PIKASIM_WEBHOOK_SECRET', '');
    }

    // ——— Account ———

    /**
     * @return array<string, mixed>
     */
    public function account(): array
    {
        return $this->data($this->request('get', '/account'));
    }

    /**
     * @return array<string, mixed>
     */
    public function balance(): array
    {
        return $this->data($this->request('get', '/balance'));
    }

    /**
     * @param  array{page?: int, limit?: int, type?: string}  $query
     * @return array<string, mixed>
     */
    public function transactions(array $query = []): array
    {
        return $this->data($this->request('get', '/transactions', $query));
    }

    // ——— Packages ———

    /**
     * List eSIM packages.
     *
     * @param  array{
     *   type?: 'data'|'phone'|'all',
     *   country?: string,
     *   region?: string,
     *   duration?: int,
     *   minData?: float|int,
     *   maxData?: float|int,
     *   privacy?: 'max'|'standard'|'all',
     *   page?: int,
     *   limit?: int
     * }  $query
     * @return array{packages: list<array<string, mixed>>, pagination?: array<string, mixed>}
     */
    public function packages(array $query = []): array
    {
        $data = $this->data($this->request('get', '/packages', $query));

        return [
            'packages' => is_array($data['packages'] ?? null) ? $data['packages'] : [],
            'pagination' => is_array($data['pagination'] ?? null) ? $data['pagination'] : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function package(string $packageCode): array
    {
        return $this->data($this->request('get', '/packages/'.rawurlencode($packageCode)));
    }

    // ——— Orders ———

    /**
     * Create an eSIM order (async — details arrive via webhook).
     *
     * @return array<string, mixed>
     */
    public function createOrder(string $packageCode, ?string $externalOrderId = null): array
    {
        $payload = ['packageCode' => $packageCode];
        if ($externalOrderId !== null && $externalOrderId !== '') {
            $payload['externalOrderId'] = $externalOrderId;
        }

        return $this->data($this->request('post', '/orders', $payload), 201);
    }

    /**
     * @param  array{status?: string, page?: int, limit?: int}  $query
     * @return array<string, mixed>
     */
    public function orders(array $query = []): array
    {
        return $this->data($this->request('get', '/orders', $query));
    }

    /**
     * @return array<string, mixed>
     */
    public function order(string $orderId): array
    {
        return $this->data($this->request('get', '/orders/'.rawurlencode($orderId)));
    }

    // ——— eSIMs ———

    /**
     * @return array<string, mixed>
     */
    public function esim(string $iccid): array
    {
        return $this->data($this->request('get', '/esims/'.rawurlencode($iccid)));
    }

    /**
     * @return array<string, mixed>
     */
    public function esimUsage(string $iccid): array
    {
        return $this->data($this->request('get', '/esims/'.rawurlencode($iccid).'/usage'));
    }

    /**
     * @return array{iccid: string, topupPackages: list<array<string, mixed>>}
     */
    public function topupOptions(string $iccid): array
    {
        $data = $this->data($this->request('get', '/esims/'.rawurlencode($iccid).'/topup-options'));

        return [
            'iccid' => (string) ($data['iccid'] ?? $iccid),
            'topupPackages' => is_array($data['topupPackages'] ?? null) ? $data['topupPackages'] : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function createTopup(string $iccid, string $packageCode, ?string $externalOrderId = null): array
    {
        $payload = ['packageCode' => $packageCode];
        if ($externalOrderId !== null && $externalOrderId !== '') {
            $payload['externalOrderId'] = $externalOrderId;
        }

        return $this->data($this->request('post', '/esims/'.rawurlencode($iccid).'/topup', $payload));
    }

    /**
     * Cancel unused data eSIM for refund. Phone plans cannot be cancelled via API.
     *
     * @return array<string, mixed>
     */
    public function cancelEsim(string $iccid): array
    {
        return $this->data($this->request('post', '/esims/'.rawurlencode($iccid).'/cancel'));
    }

    // ——— Deposits ———

    /**
     * @return array{sessionId?: string, checkoutUrl?: string, success?: bool}
     */
    public function depositStripe(int $amountCents): array
    {
        $response = $this->request('post', '/deposit/stripe', ['amount' => $amountCents]);
        $json = $response->json();

        if (!$response->successful() || !is_array($json) || empty($json['success'])) {
            throw new \RuntimeException($this->extractError($response));
        }

        return $json;
    }

    /**
     * @return array<string, mixed>
     */
    public function depositBtcpay(int $amountCents): array
    {
        $response = $this->request('post', '/deposit/btcpay', ['amount' => $amountCents]);
        $json = $response->json();

        if (!$response->successful() || !is_array($json) || empty($json['success'])) {
            throw new \RuntimeException($this->extractError($response));
        }

        return $json;
    }

    // ——— Webhooks ———

    /**
     * @param  list<string>|null  $events
     * @return array<string, mixed>
     */
    public function updateWebhook(string $webhookUrl, ?array $events = null): array
    {
        $payload = ['webhookUrl' => $webhookUrl];
        if ($events !== null) {
            $payload['webhookEvents'] = $events;
        }

        return $this->data($this->request('put', '/webhook', $payload));
    }

    /**
     * @return array<string, mixed>
     */
    public function testWebhook(): array
    {
        return $this->data($this->request('post', '/webhook/test'));
    }

    /**
     * Verify inbound webhook HMAC-SHA256 signature (X-Webhook-Signature).
     */
    public function verifyWebhookSignature(string $rawPayload, string $signature): bool
    {
        $secret = $this->webhookSecret();
        if ($secret === '' || $signature === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $rawPayload, $secret);

        return hash_equals($expected, $signature);
    }

    // ——— HTTP ———

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function request(string $method, string $path, array $payload = []): Response
    {
        if (!$this->configured()) {
            throw new \RuntimeException('PikaSIM API is not configured.');
        }

        $this->respectRateLimit();

        $url = $this->baseUrl().'/'.ltrim($path, '/');
        $pending = Http::timeout(45)
            ->acceptJson()
            ->withHeaders([
                'X-API-Key' => (string) $this->config->get('PIKASIM_API_KEY'),
            ]);

        $method = strtolower($method);
        $response = match ($method) {
            'post' => $pending->asJson()->post($url, $payload),
            'put' => $pending->asJson()->put($url, $payload),
            'patch' => $pending->asJson()->patch($url, $payload),
            'delete' => $pending->delete($url, $payload),
            default => $pending->get($url, $payload),
        };

        if (!$response->successful()) {
            Log::warning('PikaSIM API error', [
                'path' => $path,
                'method' => $method,
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);
        }

        return $response;
    }

    /**
     * Unwrap `{ success, data }` and throw on API / HTTP failure.
     *
     * @return array<string, mixed>
     */
    protected function data(Response $response, int ...$okStatuses): array
    {
        $ok = $okStatuses !== [] ? $okStatuses : [200];
        $json = $response->json();

        if (!in_array($response->status(), $ok, true) || !is_array($json)) {
            throw new \RuntimeException($this->extractError($response));
        }

        if (array_key_exists('success', $json) && empty($json['success'])) {
            throw new \RuntimeException($this->extractError($response));
        }

        $data = $json['data'] ?? $json;
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid response from PikaSIM API.');
        }

        return $data;
    }

    protected function respectRateLimit(): void
    {
        // Soft client-side spacing; upstream limit is ~60 req/min.
        $key = 'pikasim_api_last_call';
        $last = (float) Cache::get($key, 0);
        $elapsed = microtime(true) - $last;
        if ($elapsed < 0.2) {
            usleep((int) ((0.2 - $elapsed) * 1_000_000));
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
        }

        $map = [
            401 => 'Unauthorized — invalid or missing PikaSIM API key.',
            402 => 'Insufficient PikaSIM wallet balance.',
            403 => 'Forbidden — account suspended or IP not whitelisted.',
            404 => 'PikaSIM resource not found.',
            429 => 'PikaSIM rate limit exceeded. Try again shortly.',
        ];

        if (isset($map[$response->status()])) {
            return $map[$response->status()];
        }

        $body = trim($response->body());
        if ($body !== '' && strlen($body) < 300) {
            return $body;
        }

        return 'PikaSIM API request failed (HTTP '.$response->status().').';
    }
}
