<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AppConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminMaintenanceController extends Controller
{
    public function __construct(protected AppConfigService $config) {}

    public function migrate(Request $request)
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            return back()->with([
                'message' => 'Database migrations completed successfully.',
                'maintenance_output' => trim(Artisan::output()),
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Migration failed: '.$e->getMessage());
        }
    }

    public function clearCache(Request $request)
    {
        try {
            Artisan::call('optimize:clear');
            $this->config->flushCache();

            return back()->with([
                'message' => 'Application cache cleared successfully.',
                'maintenance_output' => trim(Artisan::output()),
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Cache clear failed: '.$e->getMessage());
        }
    }
}
