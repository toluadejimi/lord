<?php

namespace App\Support;

class StaticAsset
{
    public static function url(string $path): string
    {
        $path = ltrim($path, '/');

        if (self::deployedFromProjectRoot()) {
            $path = 'public/'.$path;
        }

        return asset($path);
    }

    public static function deployedFromProjectRoot(): bool
    {
        $base = base_path();

        if (!is_file($base.'/index.php')) {
            return false;
        }

        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $projectRoot = realpath($base) ?: '';
        $publicRoot = realpath(public_path()) ?: '';

        if ($docRoot !== '' && $projectRoot !== '' && $docRoot === $projectRoot) {
            return true;
        }

        return $publicRoot !== '' && $docRoot !== '' && $docRoot !== $publicRoot;
    }
}
