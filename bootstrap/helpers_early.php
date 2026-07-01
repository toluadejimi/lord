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
        if (class_exists(\App\Support\LegacyHelpers::class)) {
            return \App\Support\LegacyHelpers::getSCountries();
        }

        return [];
    }
}

if (!function_exists('get_s_product_cost')) {
    function get_s_product_cost(string $operator, string $country, string $product): float|int
    {
        if (class_exists(\App\Support\LegacyHelpers::class)) {
            return \App\Support\LegacyHelpers::getSProductCost($operator, $country, $product);
        }

        return 0;
    }
}

if (!function_exists('get_world_countries')) {
    function get_world_countries(): mixed
    {
        if (class_exists(\App\Support\LegacyHelpers::class)) {
            return \App\Support\LegacyHelpers::getWorldCountries();
        }

        return null;
    }
}

if (!function_exists('get_world_services')) {
    function get_world_services(): mixed
    {
        if (class_exists(\App\Support\LegacyHelpers::class)) {
            return \App\Support\LegacyHelpers::getWorldServices();
        }

        return null;
    }
}
