<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AppConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminMaintenanceController extends Controller
{
    public function __construct(protected AppConfigService $config) {}

    public function migrate(Request $request)
    {
        try {
            $this->syncExistingMigrationRecords();

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

    protected function syncExistingMigrationRecords(): void
    {
        if (!Schema::hasTable('migrations')) {
            Artisan::call('migrate:install', ['--force' => true]);
        }

        $legacyMigrations = [
            '2014_10_12_000000_create_users_table' => fn () => Schema::hasTable('users'),
            '2014_10_12_100000_create_password_reset_tokens_table' => fn () => Schema::hasTable('password_reset_tokens'),
            '2019_08_19_000000_create_failed_jobs_table' => fn () => Schema::hasTable('failed_jobs'),
            '2019_12_14_000001_create_personal_access_tokens_table' => fn () => Schema::hasTable('personal_access_tokens'),
            '2025_02_28_000000_create_verifications_table' => fn () => Schema::hasTable('verifications'),
            '2025_02_28_000001_create_settings_table' => fn () => Schema::hasTable('settings'),
            '2025_02_28_000002_add_columns_to_users_table' => fn () => Schema::hasTable('users') && Schema::hasColumn('users', 'username'),
            '2025_02_28_000003_create_transactions_table' => fn () => Schema::hasTable('transactions'),
            '2025_02_28_000004_create_payment_methods_table' => fn () => Schema::hasTable('payment_methods'),
            '2025_02_28_000005_create_manual_payments_table' => fn () => Schema::hasTable('manual_payments'),
            '2025_02_28_000006_create_account_details_table' => fn () => Schema::hasTable('account_details'),
        ];

        $batch = (int) DB::table('migrations')->max('batch');
        $batch = $batch > 0 ? $batch : 1;

        foreach ($legacyMigrations as $migration => $alreadyApplied) {
            if (!$alreadyApplied()) {
                continue;
            }

            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            if ($exists) {
                continue;
            }

            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch,
            ]);
        }
    }
}
