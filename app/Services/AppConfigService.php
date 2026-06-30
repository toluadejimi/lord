<?php

namespace App\Services;

use App\Models\AppConfig;
use Illuminate\Support\Facades\Cache;

class AppConfigService
{
    public function get(string $key, ?string $default = null): ?string
    {
        $cacheKey = 'app_config.'.$key;

        return Cache::remember($cacheKey, 300, function () use ($key, $default) {
            $row = AppConfig::where('config_key', $key)->first();
            if ($row && $row->config_value !== null && $row->config_value !== '') {
                return $row->config_value;
            }

            $meta = $this->keyMeta($key);
            if ($meta && !empty($meta['env'])) {
                $envVal = env($meta['env']);
                if ($envVal !== null && $envVal !== '') {
                    return (string) $envVal;
                }
            }

            if ($meta && array_key_exists('default', $meta)) {
                return (string) $meta['default'];
            }

            return $default;
        });
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $val = $this->get($key);
        if ($val === null) {
            return $default;
        }

        return in_array(strtolower((string) $val), ['1', 'true', 'yes', 'on'], true);
    }

    public function set(string $key, ?string $value): void
    {
        AppConfig::updateOrCreate(
            ['config_key' => $key],
            ['config_value' => $value]
        );
        Cache::forget('app_config.'.$key);
    }

    public function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            $this->set($key, $value === null ? null : (string) $value);
        }
    }

    public function allGrouped(): array
    {
        $stored = AppConfig::pluck('config_value', 'config_key')->toArray();
        $groups = config('platform.config_groups', []);
        $result = [];

        foreach ($groups as $groupKey => $group) {
            $result[$groupKey] = [
                'label' => $group['label'],
                'keys' => [],
            ];
            foreach ($group['keys'] as $configKey => $meta) {
                $value = $stored[$configKey] ?? null;
                if (($value === null || $value === '') && !empty($meta['env'])) {
                    $value = env($meta['env']);
                }
                if (($value === null || $value === '') && array_key_exists('default', $meta)) {
                    $value = $meta['default'];
                }
                $result[$groupKey]['keys'][$configKey] = array_merge($meta, ['value' => $value]);
            }
        }

        return $result;
    }

    public function keyMeta(string $key): ?array
    {
        foreach (config('platform.config_groups', []) as $group) {
            if (isset($group['keys'][$key])) {
                return $group['keys'][$key];
            }
        }

        return null;
    }

    public function flushCache(): void
    {
        foreach (array_keys($this->flatKeys()) as $key) {
            Cache::forget('app_config.'.$key);
        }
    }

    public function flatKeys(): array
    {
        $keys = [];
        foreach (config('platform.config_groups', []) as $group) {
            foreach ($group['keys'] as $configKey => $meta) {
                $keys[$configKey] = $meta;
            }
        }

        return $keys;
    }
}
