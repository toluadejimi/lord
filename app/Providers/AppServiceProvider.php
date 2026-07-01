<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $loader = base_path('bootstrap/load_helpers.php');
        if (is_file($loader)) {
            require_once $loader;
        }
        if (function_exists('smslord_load_helpers')) {
            smslord_load_helpers(base_path());
        }
    }

    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
