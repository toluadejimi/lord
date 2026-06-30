<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AppConfigService;
use App\Services\Payment\SprintPayVasClient;
use Illuminate\Http\Request;

class AdminVtuController extends Controller
{
    public function __construct(
        protected AppConfigService $config,
        protected SprintPayVasClient $vas,
    ) {}

    public function index()
    {
        $vtuServices = [];
        foreach (config('platform.admin_vtu_services', []) as $slug => $meta) {
            $vtuServices[$slug] = array_merge($meta, [
                'category_id' => $this->config->get($meta['category_key'], ''),
                'enabled' => $this->config->getBool($meta['enabled_key'], true),
            ]);
        }

        $sprintpayKeys = [
            'WEBKEY' => $this->maskOrEmpty($this->config->get('WEBKEY')),
            'SPRINTPAY_API_BASE' => $this->config->get('SPRINTPAY_API_BASE', 'https://web.sprintpay.online/api'),
            'PALMPAYKEY' => $this->maskOrEmpty($this->config->get('PALMPAYKEY')),
        ];

        return view('admin.vtu', [
            'vtuEnabled' => $this->config->getBool('provider_vtu_enabled', true),
            'vtuServices' => $vtuServices,
            'sprintpayKeys' => $sprintpayKeys,
            'remoteCategories' => null,
        ]);
    }

    public function update(Request $request)
    {
        $this->config->set('provider_vtu_enabled', $request->boolean('provider_vtu_enabled') ? '1' : '0');

        foreach (config('platform.admin_vtu_services', []) as $slug => $meta) {
            $this->config->set($meta['enabled_key'], $request->boolean($meta['enabled_key']) ? '1' : '0');
            if ($request->filled($meta['category_key'])) {
                $this->config->set($meta['category_key'], $request->input($meta['category_key']));
            }
        }

        if ($request->filled('WEBKEY')) {
            $this->config->set('WEBKEY', $request->input('WEBKEY'));
        }
        if ($request->filled('SPRINTPAY_API_BASE')) {
            $this->config->set('SPRINTPAY_API_BASE', $request->input('SPRINTPAY_API_BASE'));
        }
        if ($request->filled('PALMPAYKEY')) {
            $this->config->set('PALMPAYKEY', $request->input('PALMPAYKEY'));
        }

        $this->config->flushCache();

        return back()->with('message', 'VTU settings saved.');
    }

    public function fetchCategories()
    {
        try {
            $categories = $this->vas->categories();
            return back()->with('message', 'Categories fetched.')->with('remoteCategories', $categories);
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not fetch categories from provider.');
        }
    }

    protected function maskOrEmpty(?string $value): string
    {
        return $value ? '********' : '';
    }
}
