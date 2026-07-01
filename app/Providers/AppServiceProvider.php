<?php

namespace App\Providers;

use App\Services\AppConfigService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $appHelpers = app_path('helpers.php');
        if (is_file($appHelpers)) {
            require_once $appHelpers;
        }

        $this->loadLegacyHelpers();
    }

    public function boot(): void
    {
        Paginator::useBootstrap();
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
