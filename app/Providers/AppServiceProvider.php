<?php

namespace App\Providers;

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
    }

    protected function loadLegacyHelpers(): void
    {
        $legacy = base_path('bootstrap/helpers_legacy.php');
        if (is_file($legacy)) {
            require_once $legacy;
        }
    }
}
