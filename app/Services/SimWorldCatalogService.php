<?php

namespace App\Services;

use App\Models\Setting;

class SimWorldCatalogService
{
    public static function simToken(): ?string
    {
        if (function_exists('app_config')) {
            $token = app_config('SIMTOKEN');
            if ($token) {
                return $token;
            }
        }

        $env = env('SIMTOKEN');

        return $env !== null && $env !== '' ? (string) $env : null;
    }

  /**
     * @return array<string, string>
     */
    public static function countries(): array
    {
        $token = self::simToken();
        if (!$token) {
            return [];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/guest/countries');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$token,
            'Accept: application/json',
        ]);

        $var = curl_exec($ch);
        curl_close($ch);
        $inputArray = json_decode($var, true);

        if (!is_array($inputArray)) {
            return [];
        }

        $result = [];
        foreach ($inputArray as $key => $value) {
            $result[$key] = is_array($value) ? ($value['text_en'] ?? $key) : $key;
        }

        return $result;
    }

    public static function productCost(string $operator, string $country, string $product): float|int
    {
        $token = self::simToken();
        if (!$token) {
            return 0;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/guest/products/'.$country.'/'.$operator);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$token,
            'Accept: application/json',
        ]);

        $var = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($var, true);

        if (!is_array($data)) {
            return 0;
        }

        $filteredData = array_filter($data, function ($key) use ($product) {
            return stripos($key, $product) !== false;
        }, ARRAY_FILTER_USE_KEY);

        if ($filteredData === []) {
            return 0;
        }

        $prices = [];
        foreach ($filteredData as $item) {
            if (isset($item['Price'])) {
                $prices[] = $item['Price'];
            }
        }

        if ($prices === []) {
            return 0;
        }

        $sRate = Setting::find(3);
        if (!$sRate) {
            return 0;
        }

        return ($sRate->rate * $prices[0]) + $sRate->margin;
    }
}
