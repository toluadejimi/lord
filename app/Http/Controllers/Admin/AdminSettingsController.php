<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteNotification;
use App\Services\AppConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    public function __construct(protected AppConfigService $config) {}

    public function index()
    {
        $all = $this->config->allGrouped();
        $allowed = config('platform.admin_settings_groups', []);
        $groups = array_intersect_key($all, array_flip($allowed));
        $notification = SiteNotification::first();

        return view('admin.settings', compact('groups', 'notification'));
    }

    public function updateKeys(Request $request)
    {
        $pairs = [];
        $allowed = config('platform.admin_settings_groups', []);
        foreach ($this->config->flatKeys() as $key => $meta) {
            $groupKey = $this->configKeyGroup($key);
            if ($groupKey && !in_array($groupKey, $allowed, true)) {
                continue;
            }
            if ($request->has($key)) {
                $value = $request->input($key);
                if (($meta['type'] ?? 'text') === 'boolean') {
                    $value = $request->boolean($key) ? '1' : '0';
                }
                $pairs[$key] = $value;
            } elseif (($meta['type'] ?? '') === 'boolean') {
                $pairs[$key] = '0';
            }
        }

        $this->config->setMany($pairs);
        $this->config->flushCache();

        return back()->with('message', 'Platform settings saved.');
    }

    protected function configKeyGroup(string $key): ?string
    {
        foreach (config('platform.config_groups', []) as $groupKey => $group) {
            if (isset($group['keys'][$key])) {
                return $groupKey;
            }
        }

        return null;
    }

    public function updateNotification(Request $request)
    {
        SiteNotification::updateOrCreate(
            ['id' => 1],
            [
                'title' => $request->title,
                'message' => $request->message,
                'is_active' => $request->boolean('is_active'),
            ]
        );

        $this->config->set('site_notification_title', $request->title);
        $this->config->set('site_notification_message', $request->message);
        $this->config->set('site_notification_active', $request->boolean('is_active') ? '1' : '0');
        $this->config->flushCache();

        return back()->with('message', 'Sitewide notification updated.');
    }
}
