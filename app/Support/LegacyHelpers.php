<?php

namespace App\Support;

use App\Models\Setting;
use App\Services\AppConfigService;

class LegacyHelpers
{
    public static function getSCountries(): array
    {
        $token = app(AppConfigService::class)->get('SIMTOKEN');
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
            $result[$key] = $value['text_en'];
        }

        return $result;
    }

    public static function getSProductCost(string $operator, string $country, string $product): float|int
    {
        $token = app(AppConfigService::class)->get('SIMTOKEN');
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
        $var = json_decode($var);

        if (is_object($var)) {
            $data = json_decode(json_encode($var), true);
        } else {
            $data = [];
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

        $sRate = Setting::where('id', 3)->first();

        return ($sRate->rate * $prices[0]) + $sRate->margin;
    }

    public static function getWorldCountries(): mixed
    {
        $key = app(AppConfigService::class)->get('WKEY');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.smspool.net/country/retrieve_all?key=$key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $var = curl_exec($curl);
        curl_close($curl);

        return json_decode($var);
    }

    public static function getWorldServices(): mixed
    {
        $key = app(AppConfigService::class)->get('WKEY');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.smspool.net/service/retrieve_all?key=$key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $var = curl_exec($curl);
        curl_close($curl);

        return json_decode($var);
    }
}
