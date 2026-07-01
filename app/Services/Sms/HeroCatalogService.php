<?php

namespace App\Services\Sms;

class HeroCatalogService
{
    public function __construct(protected HeroHandlerProvider $provider) {}

    public function countries(string $providerKey): array
    {
        $raw = $this->provider->getCountries($providerKey);
        $json = json_decode($raw, true);

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

    public function services(string $providerKey): array
    {
        $raw = $this->provider->getServicesList($providerKey);
        $json = json_decode($raw, true);

        if (!is_array($json)) {
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
                $code = (string) ($item['code'] ?? $item['service'] ?? '');
                if ($code === '') {
                    continue;
                }
                $services[] = [
                    'code' => $code,
                    'name' => $item['name'] ?? ucfirst($code),
                ];
            }
        } else {
            foreach ($list as $code => $name) {
                if (is_array($name)) {
                    $services[] = [
                        'code' => (string) ($name['code'] ?? $code),
                        'name' => $name['name'] ?? ucfirst((string) $code),
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

    public function quote(string $providerKey, string $country, string $service): ?array
    {
        $raw = $this->provider->getPrices($providerKey, $service);
        $json = json_decode($raw, true);

        if (!is_array($json)) {
            return null;
        }

        $entry = $json[$country][$service] ?? null;

        if (!is_array($entry)) {
            foreach ($json as $countryBlock) {
                if (!is_array($countryBlock)) {
                    continue;
                }
                if (isset($countryBlock[$service]) && is_array($countryBlock[$service])) {
                    $entry = $countryBlock[$service];
                    break;
                }
            }
        }

        if (!is_array($entry)) {
            return null;
        }

        $usd = (float) ($entry['cost'] ?? $entry['price'] ?? 0);
        if ($usd <= 0) {
            return null;
        }

        return [
            'usd' => $usd,
            'available' => (int) ($entry['count'] ?? $entry['quantity'] ?? 0),
        ];
    }
}
