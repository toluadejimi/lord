<?php

namespace App\Services\Sms;

use App\Support\SmsBowerCountries;
use Illuminate\Support\Facades\Cache;

class HeroCatalogService
{
    public function __construct(protected HeroHandlerProvider $provider) {}

    public function countries(string $providerKey): array
    {
        if ($providerKey === 'sv3') {
            return SmsBowerCountries::catalog();
        }

        $raw = $this->provider->getCountries($providerKey);
        $json = $this->decodeApiJson($raw);

        if (!is_array($json)) {
            return [];
        }

        $countries = [];

        foreach ($json as $id => $item) {
            if (is_array($item)) {
                $name = $item['eng'] ?? $item['name'] ?? $item['rus'] ?? (string) $id;
            } else {
                $name = (string) $item;
            }

            $countries[] = [
                'id' => (string) $id,
                'name' => $name,
            ];
        }

        usort($countries, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $countries;
    }

    /**
     * Countries that have stock for a given service (SMS Bower: getPrices?service=).
     *
     * @return list<array{id: string, name: string, available: int, usd: float}>
     */
    public function countriesForService(string $providerKey, string $service): array
    {
        $cacheKey = 'hero_catalog.countries.'.$providerKey.'.'.$service;

        return Cache::remember($cacheKey, 300, function () use ($providerKey, $service) {
            return $this->fetchCountriesForService($providerKey, $service);
        });
    }

    public function services(string $providerKey): array
    {
        $cacheKey = 'hero_catalog.services.'.$providerKey;

        $cached = Cache::get($cacheKey);
        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $services = $this->fetchServices($providerKey);

        if ($services !== []) {
            Cache::put($cacheKey, $services, 3600);
        }

        return $services;
    }

    public function quote(string $providerKey, string $country, string $service): ?array
    {
        $raw = $this->provider->getPrices($providerKey, $service, $country);
        $json = $this->decodeApiJson($raw);

        if (!is_array($json)) {
            return null;
        }

        $entry = $this->extractPriceEntry($json, $country, $service);

        if (!is_array($entry)) {
            return null;
        }

        $usd = (float) ($entry['cost'] ?? $entry['price'] ?? $entry['retail_price'] ?? 0);
        if ($usd <= 0) {
            return null;
        }

        return [
            'usd' => $usd,
            'available' => (int) ($entry['count'] ?? $entry['quantity'] ?? $entry['phones'] ?? 0),
        ];
    }

    /**
     * @return list<array{code: string, name: string}>
     */
    protected function fetchServices(string $providerKey): array
    {
        $raw = $this->provider->getServicesList($providerKey);
        $services = $this->parseServicesPayload($raw);

        if ($services === [] && $providerKey === 'sv3') {
            $services = $this->fallbackSmsBowerServices();
        }

        return $services;
    }

    /**
     * @return list<array{id: string, name: string, available: int, usd: float}>
     */
    protected function fetchCountriesForService(string $providerKey, string $service): array
    {
        $raw = $this->provider->getPrices($providerKey, $service, null);
        $json = $this->decodeApiJson($raw);

        if (!is_array($json)) {
            return [];
        }

        $nameMap = $providerKey === 'sv3'
            ? config('smsbower_countries', [])
            : [];

        $countries = [];

        foreach ($json as $countryId => $block) {
            if (!is_array($block)) {
                continue;
            }

            $entry = $block[$service] ?? null;
            if (!is_array($entry) && isset($block['cost'])) {
                $entry = $block;
            }

            if (!is_array($entry)) {
                continue;
            }

            $usd = (float) ($entry['cost'] ?? $entry['price'] ?? $entry['retail_price'] ?? 0);
            if ($usd <= 0) {
                continue;
            }

            $id = (string) $countryId;
            $countries[] = [
                'id' => $id,
                'name' => (string) ($nameMap[$id] ?? 'Country '.$id),
                'available' => (int) ($entry['count'] ?? $entry['quantity'] ?? $entry['phones'] ?? 0),
                'usd' => $usd,
            ];
        }

        usort($countries, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $countries;
    }

    /**
     * @return list<array{code: string, name: string}>
     */
    protected function parseServicesPayload(string $raw): array
    {
        $json = $this->decodeApiJson($raw);

        if (!is_array($json)) {
            return [];
        }

        if (isset($json['status']) && strtolower((string) $json['status']) !== 'success') {
            return [];
        }

        $list = $json['services'] ?? $json;

        if (!is_array($list)) {
            return [];
        }

        $services = [];

        if (array_is_list($list)) {
            foreach ($list as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $code = (string) ($item['code'] ?? $item['service'] ?? $item['id'] ?? '');
                if ($code === '') {
                    continue;
                }
                $services[] = [
                    'code' => $code,
                    'name' => (string) ($item['name'] ?? $item['title'] ?? ucfirst($code)),
                ];
            }
        } else {
            foreach ($list as $code => $name) {
                if ($code === 'status') {
                    continue;
                }
                if (is_array($name)) {
                    $services[] = [
                        'code' => (string) ($name['code'] ?? $name['service'] ?? $code),
                        'name' => (string) ($name['name'] ?? $name['title'] ?? ucfirst((string) $code)),
                    ];
                } else {
                    $services[] = [
                        'code' => (string) $code,
                        'name' => (string) $name,
                    ];
                }
            }
        }

        usort($services, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $services;
    }

    /**
     * @return list<array{code: string, name: string}>
     */
    protected function fallbackSmsBowerServices(): array
    {
        $map = config('smsbower_services', []);
        $services = [];

        foreach ($map as $code => $name) {
            $services[] = [
                'code' => (string) $code,
                'name' => (string) $name,
            ];
        }

        usort($services, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $services;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function decodeApiJson(string $raw): ?array
    {
        $raw = trim($raw);

        if ($raw === '' || $this->isProviderErrorText($raw)) {
            return null;
        }

        $json = json_decode($raw, true);

        if (is_array($json)) {
            return $json;
        }

        $start = strcspn($raw, '{[');
        if ($start > 0) {
            $json = json_decode(substr($raw, $start), true);
        }

        return is_array($json) ? $json : null;
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

    /**
     * @param  array<string, mixed>  $json
     * @return array<string, mixed>|null
     */
    protected function extractPriceEntry(array $json, string $country, string $service): ?array
    {
        $entry = $json[$country][$service] ?? null;

        if (is_array($entry)) {
            return $entry;
        }

        if (isset($json[$country]) && is_array($json[$country]) && isset($json[$country]['cost'])) {
            return $json[$country];
        }

        foreach ($json as $countryKey => $countryBlock) {
            if (!is_array($countryBlock)) {
                continue;
            }

            if (isset($countryBlock[$service]) && is_array($countryBlock[$service])) {
                return $countryBlock[$service];
            }

            if ((string) $countryKey === $country && isset($countryBlock['cost'])) {
                return $countryBlock;
            }
        }

        return null;
    }
}
