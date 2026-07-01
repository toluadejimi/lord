<?php

namespace App\Support;

class LegacyHelpers
{
    public static function getSCountries(): array
    {
        return function_exists('get_s_countries') ? get_s_countries() : [];
    }

    public static function getSProductCost(string $operator, string $country, string $product): float|int
    {
        return function_exists('get_s_product_cost')
            ? get_s_product_cost($operator, $country, $product)
            : 0;
    }

    public static function getWorldCountries(): mixed
    {
        return function_exists('get_world_countries') ? get_world_countries() : null;
    }

    public static function getWorldServices(): mixed
    {
        return function_exists('get_world_services') ? get_world_services() : null;
    }

    public static function sendAdminNotification(string $message): void
    {
        if (function_exists('send_admin_notification')) {
            send_admin_notification($message);
        }
    }
}
