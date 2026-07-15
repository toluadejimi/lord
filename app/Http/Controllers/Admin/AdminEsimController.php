<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EsimOrder;
use App\Models\Setting;
use App\Services\AppConfigService;
use App\Services\Esim\PikaSimClient;
use App\Services\EsimOrderService;
use Illuminate\Http\Request;

class AdminEsimController extends Controller
{
    public function __construct(
        protected AppConfigService $config,
        protected PikaSimClient $client,
        protected EsimOrderService $orders,
    ) {}

    public function index()
    {
        $setting = Setting::find(EsimOrderService::SETTING_ID);

        return view('admin.esim', [
            'moduleEnabled' => $this->config->getBool('provider_pikasim_enabled', false),
            'configured' => $this->client->configured(),
            'pricingConfigured' => $this->orders->pricingConfigured(),
            'apiKeyMasked' => $this->maskOrEmpty($this->config->get('PIKASIM_API_KEY')),
            'apiBase' => $this->config->get('PIKASIM_API_BASE', 'https://pikasim.com/api/v1/reseller'),
            'webhookSecretMasked' => $this->maskOrEmpty($this->config->get('PIKASIM_WEBHOOK_SECRET')),
            'rate' => (float) ($setting->rate ?? 0),
            'margin' => (float) ($setting->margin ?? 0),
            'recentOrders' => EsimOrder::with('user')->latest()->limit(25)->get(),
        ]);
    }

    public function update(Request $request)
    {
        $this->config->set(
            'provider_pikasim_enabled',
            $request->boolean('provider_pikasim_enabled') ? '1' : '0'
        );

        if ($request->filled('PIKASIM_API_KEY')) {
            $this->config->set('PIKASIM_API_KEY', $request->input('PIKASIM_API_KEY'));
        }
        if ($request->filled('PIKASIM_API_BASE')) {
            $this->config->set('PIKASIM_API_BASE', rtrim($request->input('PIKASIM_API_BASE'), '/'));
        }
        if ($request->filled('PIKASIM_WEBHOOK_SECRET')) {
            $this->config->set('PIKASIM_WEBHOOK_SECRET', $request->input('PIKASIM_WEBHOOK_SECRET'));
        }

        Setting::where('id', EsimOrderService::SETTING_ID)->update([
            'rate' => (float) $request->input('rate', 0),
            'margin' => (float) $request->input('margin', 0),
        ]);

        $this->config->flushCache();

        return back()->with('message', 'Esim settings saved.');
    }

    public function fetchPackages()
    {
        try {
            $balance = $this->client->balance();
            $packages = $this->client->packages(['type' => 'data', 'limit' => 10]);
            $display = $this->orders->packagesForDisplay(['type' => 'data', 'limit' => 10]);

            $message = 'API OK — '.count($packages['packages']).' packages from provider.';
            if (($display['packages'] ?? []) === []) {
                $message .= ' Warning: no customer prices — set rate (₦ per $1) and enable the module.';
            }
            if (isset($balance['balance']) || isset($balance['balanceFormatted'])) {
                $message .= ' Provider balance: '.($balance['balanceFormatted'] ?? number_format((float) ($balance['balance'] ?? 0) / 100, 2));
            }

            return back()
                ->with('message', $message)
                ->with('displayPackages', $display['packages'] ?? []);
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not fetch packages: '.$e->getMessage());
        }
    }

    protected function maskOrEmpty(?string $value): string
    {
        return $value ? '********' : '';
    }
}
