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
        $cached = Cache::get('hero_catalog.countries.sv3.v3');
        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $raw = $this->provider->getCountries('sv3');
        $json = $this->decodeApiJson($raw);
        $countries = is_array($json) ? $this->parseCountryMap($json, config('smsbower_countries', [])) : [];

        if ($countries === []) {
            $countries = SmsBowerCountries::catalog();
        } else {
            $countries = $this->alignCountryIds($countries, config('smsbower_countries', []));
        }

        if ($countries !== []) {
            Cache::put('hero_catalog.countries.sv3.v3', $countries, 3600);
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
        if (isset($json['data']) && is_array($json['data'])) {
            $json = $json['data'];
        }

        $countries = [];

        if (array_is_list($json)) {
            foreach ($json as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $idStr = (string) ($item['id'] ?? $item['country'] ?? $item['country_id'] ?? '');
                if ($idStr === '') {
                    continue;
                }

                $name = $item['eng'] ?? $item['name'] ?? $item['rus'] ?? $nameFallback[$idStr] ?? $idStr;

                $countries[] = [
                    'id' => $idStr,
                    'name' => (string) $name,
                ];
            }
        } else {
            foreach ($json as $id => $item) {
                if (in_array((string) $id, ['status', 'message', 'success'], true)) {
                    continue;
                }

                $idStr = is_numeric($id) ? (string) (int) $id : (string) $id;

                if (is_array($item)) {
                    $idStr = (string) ($item['id'] ?? $item['country'] ?? $item['country_id'] ?? $idStr);
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
        }

        usort($countries, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $countries;
    }

    /**
     * @param  list<array{id: string, name: string}>  $countries
     * @param  array<string, string>  $canonical
     * @return list<array{id: string, name: string}>
     */
    protected function alignCountryIds(array $countries, array $canonical): array
    {
        $byName = [];
        foreach ($canonical as $id => $name) {
            $byName[strtolower($name)] = (string) $id;
        }

        $aliases = [
            'usa' => '187',
            'u.s.a' => '187',
            'united states of america' => '187',
            'uk' => '16',
            'great britain' => '16',
            'russia' => '0',
            'vietnam' => '10',
            'viet nam' => '10',
            'uae' => '95',
            'united arab emirates' => '95',
        ];

        foreach ($countries as $index => $country) {
            $lookup = strtolower(trim($country['name']));

            if (isset($byName[$lookup])) {
                $countries[$index]['id'] = $byName[$lookup];
            } elseif (isset($aliases[$lookup])) {
                $countries[$index]['id'] = $aliases[$lookup];
            }
        }

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
        $country = (string) (int) $country;
        $service = strtolower(trim($service));

        $actions = $providerKey === 'sv3'
            ? ['getPricesV3', 'getPrices', 'getPricesV2']
            : ['getPrices'];

        foreach ($actions as $action) {
            $raw = $this->provider->getPrices($providerKey, $service, $country, $action);
            $json = $this->decodeApiJson($raw);

            if (!is_array($json)) {
                continue;
            }

            $entry = $this->extractPriceEntry($json, $country, $service);

            if (!is_array($entry)) {
                continue;
            }

            $usd = (float) ($entry['cost'] ?? $entry['price'] ?? $entry['retail_price'] ?? $entry['physicalPrice'] ?? $entry['minPrice'] ?? 0);
            if ($usd <= 0) {
                continue;
            }

            return [
                'usd' => $usd,
                'available' => (int) ($entry['count'] ?? $entry['quantity'] ?? $entry['phones'] ?? 0),
                'provider_id' => isset($entry['provider_id']) ? (string) $entry['provider_id'] : null,
            ];
        }

        return null;
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
        if (isset($json['data']) && is_array($json['data'])) {
            $json = $json['data'];
        }

        $countryKeys = array_values(array_unique([
            $country,
            (string) (int) $country,
            ltrim($country, '0') !== '' ? ltrim($country, '0') : $country,
        ]));

        $serviceKeys = array_values(array_unique([
            $service,
            strtolower($service),
        ]));

        foreach ($countryKeys as $countryKey) {
            foreach ($serviceKeys as $serviceKey) {
                $entry = $json[$countryKey][$serviceKey] ?? null;
                if (is_array($entry)) {
                    $resolved = $this->normalizePriceEntry($entry);
                    if ($resolved !== null) {
                        return $resolved;
                    }
                }
            }

            if (isset($json[$countryKey]) && is_array($json[$countryKey])) {
                $resolved = $this->normalizePriceEntry($json[$countryKey]);
                if ($resolved !== null) {
                    return $resolved;
                }
            }
        }

        foreach ($serviceKeys as $serviceKey) {
            if (isset($json[$serviceKey]) && is_array($json[$serviceKey])) {
                $resolved = $this->normalizePriceEntry($json[$serviceKey]);
                if ($resolved !== null) {
                    return $resolved;
                }
            }
        }

        foreach ($json as $countryKey => $countryBlock) {
            if (!is_array($countryBlock)) {
                continue;
            }

            foreach ($serviceKeys as $serviceKey) {
                if (isset($countryBlock[$serviceKey]) && is_array($countryBlock[$serviceKey])) {
                    $resolved = $this->normalizePriceEntry($countryBlock[$serviceKey]);
                    if ($resolved !== null) {
                        return $resolved;
                    }
                }
            }

            if (in_array((string) $countryKey, $countryKeys, true)) {
                $resolved = $this->normalizePriceEntry($countryBlock);
                if ($resolved !== null) {
                    return $resolved;
                }
            }
        }

        return null;
    }

    /**
     * Flat cost/count entry or best offer from getPricesV3 provider map.
     *
     * @param  array<string, mixed>  $block
     * @return array<string, mixed>|null
     */
    protected function normalizePriceEntry(array $block): ?array
    {
        if ($this->priceEntryHasCost($block)) {
            return $block;
        }

        return $this->bestProviderOffer($block);
    }

    /**
     * @param  array<string, mixed>  $providers
     * @return array<string, mixed>|null
     */
    protected function bestProviderOffer(array $providers): ?array
    {
        $cheapest = null;
        $totalCount = 0;

        foreach ($providers as $key => $offer) {
            if (!is_array($offer)) {
                continue;
            }

            if (!$this->priceEntryHasCost($offer)) {
                continue;
            }

            $price = (float) ($offer['price'] ?? $offer['cost'] ?? $offer['physicalPrice'] ?? $offer['minPrice'] ?? 0);
            $count = (int) ($offer['count'] ?? $offer['quantity'] ?? $offer['phones'] ?? 0);
            $totalCount += $count;

            if ($cheapest === null || $price < (float) $cheapest['cost']) {
                $cheapest = [
                    'cost' => $price,
                    'count' => $count,
                    'provider_id' => (string) ($offer['provider_id'] ?? $key),
                ];
            }
        }

        if ($cheapest === null) {
            return null;
        }

        if ($totalCount > (int) $cheapest['count']) {
            $cheapest['count'] = $totalCount;
        }

        return $cheapest;
    }

    protected function priceEntryHasCost(array $entry): bool
    {
        foreach (['cost', 'price', 'retail_price', 'physicalPrice', 'minPrice'] as $field) {
            if (isset($entry[$field]) && (float) $entry[$field] > 0) {
                return true;
            }
        }

        return false;
    }
}
