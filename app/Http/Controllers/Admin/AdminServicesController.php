<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AppConfigService;
use Illuminate\Http\Request;

class AdminServicesController extends Controller
{
    public function __construct(protected AppConfigService $config) {}

    public function index()
    {
        $settings = Setting::orderBy('id')->get()->keyBy('id');
        $services = [];

        foreach (config('platform.admin_service_groups', []) as $groupKey => $meta) {
            $group = config("platform.config_groups.{$groupKey}");
            if (!$group) {
                continue;
            }
            $stored = \App\Models\AppConfig::pluck('config_value', 'config_key')->toArray();
            $keys = [];
            foreach ($group['keys'] as $configKey => $keyMeta) {
                $value = $stored[$configKey] ?? null;
                if (($value === null || $value === '') && !empty($keyMeta['env'])) {
                    $value = env($keyMeta['env']);
                }
                if (($value === null || $value === '') && array_key_exists('default', $keyMeta)) {
                    $value = $keyMeta['default'];
                }
                $keys[$configKey] = array_merge($keyMeta, ['value' => $value]);
            }
            $settingId = $meta['setting_id'];
            $services[$groupKey] = array_merge($meta, [
                'label' => $meta['menu_label'] ?? $group['label'],
                'config_label' => $group['label'],
                'keys' => $keys,
                'setting' => $settings->get($settingId),
                'setting_id' => $settingId,
            ]);
        }

        return view('admin.services', compact('services'));
    }

    public function update(Request $request)
    {
        $groupKey = $request->input('service_group');
        $group = config("platform.config_groups.{$groupKey}");
        if (!$group || !config("platform.admin_service_groups.{$groupKey}")) {
            return back()->with('error', 'Invalid service.');
        }

        $pairs = [];
        foreach ($group['keys'] as $configKey => $meta) {
            if ($request->has($configKey)) {
                $value = $request->input($configKey);
                if (($meta['type'] ?? 'text') === 'boolean') {
                    $value = $request->boolean($configKey) ? '1' : '0';
                }
                $pairs[$configKey] = $value;
            } elseif (($meta['type'] ?? '') === 'boolean') {
                $pairs[$configKey] = '0';
            }
        }
        $this->config->setMany($pairs);

        $settingId = config("platform.admin_service_groups.{$groupKey}.setting_id");
        if ($settingId && $request->has('rate')) {
            Setting::where('id', $settingId)->update([
                'rate' => $request->input('rate', 0),
                'margin' => $request->input('margin', 0),
                'is_enabled' => $request->boolean('pricing_enabled'),
            ]);
        }

        $this->config->flushCache();

        return back()->with('message', 'Service settings saved.');
    }
}
