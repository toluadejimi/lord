<?php

namespace App\Services\Payment;

use App\Services\AppConfigService;
use Illuminate\Support\Facades\Http;

class SprintPayVasClient
{
    public function __construct(protected AppConfigService $config) {}

    protected function base(): string
    {
        return rtrim($this->config->get('SPRINTPAY_API_BASE', 'https://web.sprintpay.online/api'), '/');
    }

    protected function key(): string
    {
        return (string) $this->config->get('WEBKEY', '');
    }

    public function categories(): array
    {
        $response = Http::asForm()->post($this->base().'/vas/categories', ['key' => $this->key()]);

        return $response->json() ?? [];
    }

    public function variations(string $categoryId): array
    {
        $response = Http::asForm()->post($this->base().'/vas/variations', [
            'key' => $this->key(),
            'category_id' => $categoryId,
        ]);

        return $response->json() ?? [];
    }

    public function validate(string $categoryId, string $billersCode, string $type = 'cable'): array
    {
        $response = Http::asForm()->post($this->base().'/vas/validate', [
            'key' => $this->key(),
            'category_id' => $categoryId,
            'billersCode' => $billersCode,
            'type' => $type,
        ]);

        return $response->json() ?? [];
    }

    public function purchase(array $payload): array
    {
        $response = Http::asForm()->post($this->base().'/vas/purchase', array_merge([
            'key' => $this->key(),
        ], $payload));

        return $response->json() ?? [];
    }
}
