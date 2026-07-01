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
            base_path('smslord_bootstrap.php'),
            app_path('helpers.php'),
            base_path('bootstrap/helpers_bootstrap.php'),
            base_path('bootstrap/helpers_early.php'),
            base_path('bootstrap/helpers_legacy.php'),
        ] as $helperFile) {
            if (is_file($helperFile)) {
                require_once $helperFile;
            }
        }
    }
}
