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

        $this->configureAssetUrlForDocumentRoot();
    }

    protected function configureAssetUrlForDocumentRoot(): void
    {
        if (config('app.asset_url')) {
            return;
        }

        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $projectRoot = realpath(base_path()) ?: '';

        if ($docRoot !== '' && $docRoot === $projectRoot) {
            config(['app.asset_url' => rtrim(config('app.url'), '/').'/public']);
        }
    }
}
