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
            'SPRINTPAY_WEBHOOK_SECRET' => $this->maskOrEmpty($this->config->get('SPRINTPAY_WEBHOOK_SECRET')),
            'SPRINTPAY_API_BASE' => $this->config->get('SPRINTPAY_API_BASE', 'https://web.sprintpay.online/api'),
            'PALMPAYKEY' => $this->maskOrEmpty($this->config->get('PALMPAYKEY')),
        ];

        return view('admin.vtu', [
            'vtuEnabled' => $this->config->getBool('provider_vtu_enabled', true),
            'vtuConfigured' => $this->vas->configured(),
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
        if ($request->filled('SPRINTPAY_WEBHOOK_SECRET')) {
            $this->config->set('SPRINTPAY_WEBHOOK_SECRET', $request->input('SPRINTPAY_WEBHOOK_SECRET'));
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
            $parsed = $this->parseCategoriesForDisplay($categories);

            return back()
                ->with('message', 'Categories fetched — copy each ID into the matching service below, then Save.')
                ->with('remoteCategories', $categories)
                ->with('parsedCategories', $parsed);
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not fetch categories from provider. Check your SprintPay API key (WEBKEY) is saved first.');
        }
    }

    protected function parseCategoriesForDisplay(array $raw): array
    {
        $rows = [];

        $list = $raw['data'] ?? $raw['categories'] ?? $raw['content'] ?? $raw;

        if (isset($list['data']) && is_array($list['data'])) {
            $list = $list['data'];
        }

        if (!is_array($list)) {
            return $rows;
        }

        if (array_is_list($list)) {
            foreach ($list as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $id = $item['id'] ?? $item['category_id'] ?? $item['ID'] ?? null;
                $name = $item['name'] ?? $item['title'] ?? $item['category_name'] ?? null;
                if ($id !== null && $name !== null) {
                    $rows[] = ['id' => (string) $id, 'name' => (string) $name];
                }
            }
        } else {
            foreach ($list as $id => $name) {
                if (is_array($name)) {
                    $rows[] = [
                        'id' => (string) ($name['id'] ?? $name['category_id'] ?? $id),
                        'name' => (string) ($name['name'] ?? $name['title'] ?? $id),
                    ];
                } else {
                    $rows[] = ['id' => (string) $id, 'name' => (string) $name];
                }
            }
        }

        usort($rows, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $rows;
    }

    protected function maskOrEmpty(?string $value): string
    {
        return $value ? '********' : '';
    }
}
