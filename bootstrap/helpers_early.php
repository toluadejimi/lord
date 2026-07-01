<?php

if (!function_exists('deployed_from_project_root')) {
    function deployed_from_project_root(): bool
    {
        $base = dirname(__DIR__);

        if (!is_file($base.'/index.php')) {
            return false;
        }

        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $projectRoot = realpath($base) ?: '';
        $publicRoot = realpath($base.'/public') ?: '';

        if ($docRoot !== '' && $projectRoot !== '' && $docRoot === $projectRoot) {
            return true;
        }

        return $publicRoot !== '' && $docRoot !== '' && $docRoot !== $publicRoot;
    }
}

if (!function_exists('static_asset')) {
    function static_asset(string $path): string
    {
        $path = ltrim($path, '/');

        if (deployed_from_project_root()) {
            $path = 'public/'.$path;
        }

        if (function_exists('asset')) {
            return asset($path);
        }

        $root = '';
        if (function_exists('config')) {
            try {
                $root = rtrim((string) config('app.url'), '/');
            } catch (\Throwable $e) {
            }
        }

        if ($root === '' && !empty($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $root = $scheme.'://'.$_SERVER['HTTP_HOST'];
        }

        return $root === '' ? '/'.$path : $root.'/'.$path;
    }
}
