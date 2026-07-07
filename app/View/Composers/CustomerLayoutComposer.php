<?php

namespace App\View\Composers;

use App\Services\AppConfigService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerLayoutComposer
{
    public function compose(View $view): void
    {
        if (!Auth::check()) {
            $view->with('navVerificationServers', collect());

            return;
        }

        $config = app(AppConfigService::class);

        $verificationServers = collect(config('platform.admin_service_groups', []))
            ->filter(fn ($svc) => !empty($svc['user_route']) && !empty($svc['enabled_key']))
            ->map(function ($svc) use ($config) {
                return array_merge($svc, [
                    'enabled' => $config->getBool(
                        $svc['enabled_key'],
                        (bool) ($svc['enabled_default'] ?? false)
                    ),
                ]);
            })
            ->filter(fn ($svc) => $svc['enabled'])
            ->values();

        $view->with('navVerificationServers', $verificationServers);
    }
}
