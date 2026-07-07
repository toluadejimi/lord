<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Services\AppConfigService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(AppConfigService $config)
    {
        $user = Auth::user();

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

        $numbersUrl = $verificationServers->isNotEmpty()
            ? url(ltrim($verificationServers->first()['user_route'], '/'))
            : url('orders');

        $vtuOn = $config->getBool('provider_vtu_enabled', true);
        $tbtOn = $config->getBool('provider_telegram_blue_tick_enabled', false);

        $popularServices = collect([
            [
                'label' => 'Airtime',
                'icon' => 'ti-phone',
                'tone' => 'emerald',
                'url' => url('vas/airtime'),
                'enabled' => $vtuOn && $config->getBool('vtu_airtime_enabled', true),
            ],
            [
                'label' => 'Data',
                'icon' => 'ti-wifi',
                'tone' => 'blue',
                'url' => url('vas/data'),
                'enabled' => $vtuOn && $config->getBool('vtu_data_enabled', true),
            ],
            [
                'label' => 'Numbers',
                'icon' => 'ti-device-mobile',
                'tone' => 'violet',
                'url' => $numbersUrl,
                'enabled' => $verificationServers->isNotEmpty(),
            ],
            [
                'label' => 'Logs',
                'icon' => 'ti-receipt',
                'tone' => 'slate',
                'url' => url('wallet-transactions'),
                'enabled' => true,
            ],
            [
                'label' => 'Cable TV',
                'icon' => 'ti-device-tv',
                'tone' => 'amber',
                'url' => url('vas/cable'),
                'enabled' => $vtuOn && $config->getBool('vtu_cable_enabled', true),
            ],
            [
                'label' => 'Telegram',
                'icon' => 'fab fa-telegram',
                'tone' => 'sky',
                'url' => route('telegram-blue-tick.index'),
                'enabled' => $tbtOn,
            ],
            [
                'label' => 'Orders',
                'icon' => 'ti-messages',
                'tone' => 'indigo',
                'url' => url('orders'),
                'enabled' => $verificationServers->isNotEmpty(),
            ],
            [
                'label' => 'Fund',
                'icon' => 'ti-wallet',
                'tone' => 'rose',
                'url' => url('fund-wallet'),
                'enabled' => true,
            ],
        ])->filter(fn ($item) => $item['enabled'])->values();

        $recentOrders = Verification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'wallet' => (float) $user->wallet,
            'displayName' => $user->username ?: explode('@', $user->email)[0],
            'popularServices' => $popularServices,
            'verificationServers' => $verificationServers,
            'recentOrders' => $recentOrders,
        ]);
    }
}
