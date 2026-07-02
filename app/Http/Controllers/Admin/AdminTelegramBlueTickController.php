<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TelegramPremiumOrder;
use App\Services\AppConfigService;
use App\Services\TelegramPremium\IStarClient;
use App\Services\TelegramPremiumOrderService;
use Illuminate\Http\Request;

class AdminTelegramBlueTickController extends Controller
{
    public function __construct(
        protected AppConfigService $config,
        protected IStarClient $istar,
        protected TelegramPremiumOrderService $orders,
    ) {}

    public function index()
    {
        $setting = Setting::find(7);

        return view('admin.telegram-blue-tick', [
            'moduleEnabled' => $this->config->getBool('provider_telegram_blue_tick_enabled', false),
            'configured' => $this->istar->configured(),
            'apiKeyMasked' => $this->maskOrEmpty($this->config->get('ISTAR_API_KEY')),
            'apiBase' => $this->config->get('ISTAR_API_BASE', 'https://v1.fragmentapi.com/api/v1/partner'),
            'webhookSecretMasked' => $this->maskOrEmpty($this->config->get('ISTAR_WEBHOOK_SECRET')),
            'fixedPrices' => [
                3 => $this->config->get('telegram_premium_price_3', ''),
                6 => $this->config->get('telegram_premium_price_6', ''),
                12 => $this->config->get('telegram_premium_price_12', ''),
            ],
            'rate' => (float) ($setting->rate ?? 0),
            'margin' => (float) ($setting->margin ?? 0),
            'remotePackages' => null,
            'recentOrders' => TelegramPremiumOrder::with('user')->latest()->limit(25)->get(),
        ]);
    }

    public function update(Request $request)
    {
        $this->config->set(
            'provider_telegram_blue_tick_enabled',
            $request->boolean('provider_telegram_blue_tick_enabled') ? '1' : '0'
        );

        if ($request->filled('ISTAR_API_KEY')) {
            $this->config->set('ISTAR_API_KEY', $request->input('ISTAR_API_KEY'));
        }
        if ($request->filled('ISTAR_API_BASE')) {
            $this->config->set('ISTAR_API_BASE', rtrim($request->input('ISTAR_API_BASE'), '/'));
        }
        if ($request->filled('ISTAR_WEBHOOK_SECRET')) {
            $this->config->set('ISTAR_WEBHOOK_SECRET', $request->input('ISTAR_WEBHOOK_SECRET'));
        }

        foreach ([3, 6, 12] as $months) {
            $key = 'telegram_premium_price_'.$months;
            $value = $request->input($key);
            $this->config->set($key, $value !== null && $value !== '' ? (string) $value : '');
        }

        Setting::where('id', 7)->update([
            'rate' => (float) $request->input('rate', 0),
            'margin' => (float) $request->input('margin', 0),
        ]);

        $this->config->flushCache();

        return back()->with('message', 'Telegram Blue Tick settings saved.');
    }

    public function fetchPackages()
    {
        try {
            $packages = $this->istar->premiumPackages();
            $display = $this->orders->packagesForDisplay();

            return back()
                ->with('message', 'Packages fetched from iStar. Customer prices use rate/margin or fixed NGN overrides.')
                ->with('remotePackages', $packages)
                ->with('displayPackages', $display);
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not fetch packages: '.$e->getMessage());
        }
    }

    protected function maskOrEmpty(?string $value): string
    {
        return $value ? '********' : '';
    }
}
