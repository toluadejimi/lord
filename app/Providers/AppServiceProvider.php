<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadProjectHelpers();
    }

    public function boot(): void
    {
        Paginator::useBootstrap();
    }

    protected function loadProjectHelpers(): void
    {
        foreach ([
            app_path('helpers.php'),
            base_path('bootstrap/helpers_bootstrap.php'),
            base_path('smslord_bootstrap.php'),
            base_path('bootstrap/helpers_early.php'),
            base_path('bootstrap/helpers_legacy.php'),
        ] as $helperFile) {
            if (!is_file($helperFile)) {
                continue;
            }
            try {
                require_once $helperFile;
            } catch (\Throwable) {
                continue;
            }
            if (function_exists('static_asset')) {
                break;
            }
        }
    }
}
