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
    }

    protected function loadLegacyHelpers(): void
    {
        $bootstrap = base_path('bootstrap/helpers_bootstrap.php');
        if (is_file($bootstrap)) {
            require_once $bootstrap;
        }

        $early = base_path('bootstrap/helpers_early.php');
        if (is_file($early)) {
            require_once $early;
        }

        $legacy = base_path('bootstrap/helpers_legacy.php');
        if (is_file($legacy)) {
            require_once $legacy;
        }
    }
}
