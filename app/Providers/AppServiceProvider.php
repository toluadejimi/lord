<?php

namespace App\Providers;

use App\Services\AppConfigService;
use App\Support\StaticAsset;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerGlobalFunctions();
        $this->loadLegacyHelpers();
    }

    public function boot(): void
    {
        Paginator::useBootstrap();
    }

    protected function registerGlobalFunctions(): void
    {
        if (!function_exists('static_asset')) {
            function static_asset(string $path): string
            {
                return StaticAsset::url($path);
            }
        }

        if (!function_exists('deployed_from_project_root')) {
            function deployed_from_project_root(): bool
            {
                return StaticAsset::deployedFromProjectRoot();
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

        if (!function_exists('app_config')) {
            function app_config(string $key, ?string $default = null): ?string
            {
                return app(AppConfigService::class)->get($key, $default);
            }
        }

        if (!function_exists('app_config_bool')) {
            function app_config_bool(string $key, bool $default = false): bool
            {
                return app(AppConfigService::class)->getBool($key, $default);
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
    }

    protected function loadLegacyHelpers(): void
    {
        $legacy = base_path('bootstrap/helpers_legacy.php');
        if (is_file($legacy)) {
            require_once $legacy;
        }
    }
}
