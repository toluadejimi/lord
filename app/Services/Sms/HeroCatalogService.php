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
            return $this->fetchSmsBowerCountries();
        }

        $raw = $this->provider->getCountries($providerKey);
        $json = $this->decodeApiJson($raw);

        if (!is_array($json)) {
            return [];
        }

        return $this->parseCountryMap($json);
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    protected function fetchSmsBowerCountries(): array
    {
        $cached = Cache::get('hero_catalog.countries.sv3');
        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $raw = $this->provider->getCountries('sv3');
        $json = $this->decodeApiJson($raw);
        $countries = is_array($json) ? $this->parseCountryMap($json, config('smsbower_countries', [])) : [];

        if ($countries === []) {
            $countries = SmsBowerCountries::catalog();
        }

        if ($countries !== []) {
            Cache::put('hero_catalog.countries.sv3', $countries, 3600);
        }

        return $countries;
    }

    /**
     * @param  array<string, mixed>  $json
     * @param  array<string, string>  $nameFallback
     * @return list<array{id: string, name: string}>
     */
    protected function parseCountryMap(array $json, array $nameFallback = []): array
    {
        $countries = [];

        foreach ($json as $id => $item) {
            $idStr = (string) $id;

            if (is_array($item)) {
                $name = $item['eng'] ?? $item['name'] ?? $item['rus'] ?? $nameFallback[$idStr] ?? $idStr;
            } else {
                $name = (string) $item;
            }

            if (isset($nameFallback[$idStr]) && ($name === $idStr || strlen((string) $name) < 3)) {
                $name = $nameFallback[$idStr];
            }

            $countries[] = [
                'id' => $idStr,
                'name' => (string) $name,
            ];
        }

        usort($countries, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $countries;
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
