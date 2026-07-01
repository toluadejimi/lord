<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $appDir = dirname(__DIR__);
        foreach ([
            $appDir.'/Http/Helpers/helpers.php',
            $appDir.'/helpers.php',
        ] as $helpersFile) {
            if (is_file($helpersFile)) {
                require_once $helpersFile;
                break;
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
