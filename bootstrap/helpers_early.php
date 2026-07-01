<?php

if (!function_exists('deployed_from_project_root')) {
    function deployed_from_project_root(): bool
    {
        $base = dirname(__DIR__);

        if (!is_file($base.'/index.php')) {
            return false;
        }

        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $projectRoot = realpath($base) ?: '';
        $publicRoot = realpath($base.'/public') ?: '';

        if ($docRoot !== '' && $projectRoot !== '' && $docRoot === $projectRoot) {
            return true;
        }

        return $publicRoot !== '' && $docRoot !== '' && $docRoot !== $publicRoot;
    }
}

if (!function_exists('static_asset')) {
    function static_asset(string $path): string
    {
        $path = ltrim($path, '/');

        if (deployed_from_project_root()) {
            $path = 'public/'.$path;
        }

        if (function_exists('asset')) {
            return asset($path);
        }

        $root = '';
        if (function_exists('config')) {
            try {
                $root = rtrim((string) config('app.url'), '/');
            } catch (\Throwable $e) {
            }
        }

        if ($root === '' && !empty($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $root = $scheme.'://'.$_SERVER['HTTP_HOST'];
        }

        return $root === '' ? '/'.$path : $root.'/'.$path;
    }
}

if (!function_exists('app_config')) {
    function app_config(string $key, ?string $default = null): ?string
    {
        return app(\App\Services\AppConfigService::class)->get($key, $default);
    }
}

if (!function_exists('app_config_bool')) {
    function app_config_bool(string $key, bool $default = false): bool
    {
        return app(\App\Services\AppConfigService::class)->getBool($key, $default);
    }
}

if (!function_exists('get_s_countries')) {
    function get_s_countries(): array
    {
        if (!function_exists('app_config')) {
            return [];
        }

        $token = app_config('SIMTOKEN');
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
            $result[$key] = $value['text_en'];
        }

        return $result;
    }
}

if (!function_exists('get_s_product_cost')) {
    function get_s_product_cost(string $operator, string $country, string $product): float|int
    {
        if (!function_exists('app_config')) {
            return 0;
        }

        $token = app_config('SIMTOKEN');
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

        if ($prices === []) {
            return 0;
        }

        $sRate = \App\Models\Setting::where('id', 3)->first();
        if (!$sRate) {
            return 0;
        }

        return ($sRate->rate * $prices[0]) + $sRate->margin;
    }
}

if (!function_exists('get_world_countries')) {
    function get_world_countries(): mixed
    {
        if (!function_exists('app_config')) {
            return null;
        }

        $key = app_config('WKEY');
        if (!$key) {
            return null;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.smspool.net/country/retrieve_all?key=$key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
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

if (!function_exists('get_world_services')) {
    function get_world_services(): mixed
    {
        if (!function_exists('app_config')) {
            return null;
        }

        $key = app_config('WKEY');
        if (!$key) {
            return null;
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.smspool.net/service/retrieve_all?key=$key",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
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
