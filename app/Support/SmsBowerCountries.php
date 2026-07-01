<?php

namespace App\Support;

class SmsBowerCountries
{
    /**
     * @return list<array{id: string, name: string}>
     */
    public static function catalog(): array
    {
        $map = config('smsbower_countries', []);

        $countries = [];

        foreach ($map as $id => $name) {
            $countries[] = [
                'id' => (string) $id,
                'name' => (string) $name,
            ];
        }

        usort($countries, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $countries;
    }

    public static function nameForId(string $id): ?string
    {
        $name = config('smsbower_countries.'.$id);

        return is_string($name) ? $name : null;
    }
}
