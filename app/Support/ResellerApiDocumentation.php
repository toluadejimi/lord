<?php

namespace App\Support;

class ResellerApiDocumentation
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function sections(string $baseUrl): array
    {
        $baseUrl = rtrim($baseUrl, '/');

        return [
            'shared' => [
                'label' => 'Shared endpoints',
                'intro' => 'These work for every server after you rent a number. Always save the order_id from the rent response.',
                'workflow' => [
                    'Rent on any server-N/rent endpoint (see Server 1–4 tabs).',
                    'Poll POST /get-sms with {"order_id": YOUR_ID} every 10–15 seconds until status is 2.',
                    'Or configure a webhook — OTP is pushed when the SMS arrives.',
                    'Cancel with POST /cancel-sms if you no longer need the number (wallet refunded when allowed).',
                ],
                'endpoints' => [
                    self::ep($baseUrl, 'GET/POST', 'balance', 'Wallet balance', null, [
                        'success' => true,
                        'balance' => 10410.03,
                    ]),
                    self::ep($baseUrl, 'POST', 'get-sms', 'Poll OTP (all servers)', [
                        'order_id' => 12345,
                    ], [
                        'success' => true,
                        'server' => 'Server 1',
                        'status' => 2,
                        'code' => '123456',
                        'full_sms' => 'Your code is 123456',
                        'order_id' => 12345,
                        'phone' => '447911123456',
                    ], 'status: 1 = waiting · 2 = completed · 99 = cancelled'),
                    self::ep($baseUrl, 'POST', 'cancel-sms', 'Cancel & refund (all servers)', [
                        'order_id' => 12345,
                    ], [
                        'success' => true,
                        'server' => 'Server 2',
                        'message' => 'Order cancelled and wallet refunded.',
                    ], 'Server 2 orders cannot be cancelled within 120 seconds of creation.'),
                ],
            ],
            'server1' => [
                'label' => 'Server 1 — International (5SIM)',
                'intro' => 'Browse countries, pick operator + product from the prices catalog, quote NGN cost, then rent.',
                'workflow' => [
                    'GET server-1/countries — list country slugs (e.g. england, usa).',
                    'GET server-1/prices with country — returns operators, products, usd_cost, stock.',
                    'POST server-1/price — confirm NGN charge before buying.',
                    'POST server-1/rent — debit wallet, receive phone + order_id.',
                    'POST get-sms with order_id — poll until OTP arrives.',
                ],
                'endpoints' => [
                    self::ep($baseUrl, 'GET/POST', 'server-1/countries', 'List countries', null, [
                        'success' => true,
                        'countries' => ['england' => 'England', 'usa' => 'USA'],
                    ]),
                    self::ep($baseUrl, 'GET/POST', 'server-1/prices', 'Catalog for a country', [
                        'country' => 'england',
                    ], [
                        'success' => true,
                        'country' => 'england',
                        'data' => ['england' => ['whatsapp' => ['vodafone' => ['cost' => 0.45, 'count' => 120]]]],
                    ], 'GET: ?country=england · POST: JSON body below'),
                    self::ep($baseUrl, 'POST', 'server-1/price', 'Quote NGN price', [
                        'country' => 'england',
                        'operator' => 'vodafone',
                        'product' => 'whatsapp',
                        'usd_cost' => 0.45,
                    ], [
                        'success' => true,
                        'usd' => 0.45,
                        'price' => 1633.5,
                        'country' => 'england',
                        'operator' => 'vodafone',
                        'product' => 'whatsapp',
                    ], 'usd_cost optional if taken from server-1/prices'),
                    self::ep($baseUrl, 'POST', 'server-1/rent', 'Buy number', [
                        'country' => 'england',
                        'operator' => 'vodafone',
                        'product' => 'whatsapp',
                        'usd_cost' => 0.45,
                    ], [
                        'success' => true,
                        'server' => 'Server 1',
                        'order_id' => 12345,
                        'phone' => '447911123456',
                        'service' => 'whatsapp',
                        'country' => 'england',
                        'price' => 1633.5,
                        'provider_order_id' => '987654',
                    ], 'Save order_id — use it for get-sms and cancel-sms'),
                ],
            ],
            'server2' => [
                'label' => 'Server 2 — USA numbers',
                'intro' => 'US-only services. Optional area_code or carrier adds a 20% surcharge.',
                'workflow' => [
                    'GET server-2/services — list service keys (whatsapp, google, …).',
                    'POST server-2/price — quote with service + optional area_code.',
                    'POST server-2/rent — buy the number, save order_id.',
                    'POST get-sms — poll for OTP (120s cancel cooldown applies).',
                ],
                'endpoints' => [
                    self::ep($baseUrl, 'GET/POST', 'server-2/services', 'List US services', null, [
                        'success' => true,
                        'services' => ['whatsapp' => 'WhatsApp', 'google' => 'Google'],
                    ]),
                    self::ep($baseUrl, 'POST', 'server-2/price', 'Quote US number', [
                        'service' => 'whatsapp',
                        'usd_cost' => 1.0,
                        'area_code' => '212',
                        'carrier' => '',
                    ], [
                        'success' => true,
                        'service' => 'whatsapp',
                        'usd' => 1.0,
                        'price' => 4975.0,
                    ], 'area_code or carrier → +20% on USD cost'),
                    self::ep($baseUrl, 'POST', 'server-2/rent', 'Buy US number', [
                        'service' => 'whatsapp',
                        'usd_cost' => 1.0,
                        'area_code' => '212',
                    ], [
                        'success' => true,
                        'server' => 'Server 2',
                        'order_id' => 12346,
                        'phone' => '15182315891',
                        'service' => 'whatsapp',
                        'country' => 'US',
                        'price' => 4975.0,
                        'provider_order_id' => 'usa-abc',
                    ]),
                ],
            ],
            'server3' => [
                'label' => 'Server 3 — Global catalog',
                'intro' => 'Country + service IDs from the catalog endpoints. Price includes availability check.',
                'workflow' => [
                    'GET server-3/countries — country id + name list.',
                    'GET server-3/services — service id + name list.',
                    'POST server-3/price — quote with country id + service id.',
                    'POST server-3/rent — purchase number, save order_id.',
                    'POST get-sms — poll for OTP.',
                ],
                'endpoints' => [
                    self::ep($baseUrl, 'GET/POST', 'server-3/countries', 'List countries', null, [
                        'success' => true,
                        'countries' => [['id' => '12', 'name' => 'United Kingdom']],
                    ]),
                    self::ep($baseUrl, 'GET/POST', 'server-3/services', 'List services', null, [
                        'success' => true,
                        'services' => [['id' => 'wa', 'name' => 'WhatsApp']],
                    ]),
                    self::ep($baseUrl, 'POST', 'server-3/price', 'Quote price', [
                        'country' => '12',
                        'service' => 'wa',
                    ], [
                        'success' => true,
                        'country' => '12',
                        'service' => 'wa',
                        'usd' => 0.55,
                        'price' => 1850.0,
                        'available' => 42,
                    ]),
                    self::ep($baseUrl, 'POST', 'server-3/rent', 'Buy number', [
                        'country' => '12',
                        'service' => 'wa',
                    ], [
                        'success' => true,
                        'server' => 'Server 3',
                        'order_id' => 12347,
                        'phone' => '447700900123',
                        'service' => 'wa',
                        'country' => '12',
                        'price' => 1850.0,
                        'provider_order_id' => 'hero-xyz',
                    ]),
                ],
            ],
            'server4' => [
                'label' => 'Server 4 — Global catalog',
                'intro' => 'Same flow as Server 3 with a separate inventory pool.',
                'workflow' => [
                    'GET server-4/countries — country id + name list.',
                    'GET server-4/services — service id + name list.',
                    'POST server-4/price — quote with country id + service id.',
                    'POST server-4/rent — purchase number, save order_id.',
                    'POST get-sms — poll for OTP.',
                ],
                'endpoints' => [
                    self::ep($baseUrl, 'GET/POST', 'server-4/countries', 'List countries', null, [
                        'success' => true,
                        'countries' => [['id' => '12', 'name' => 'United Kingdom']],
                    ]),
                    self::ep($baseUrl, 'GET/POST', 'server-4/services', 'List services', null, [
                        'success' => true,
                        'services' => [['id' => 'wa', 'name' => 'WhatsApp']],
                    ]),
                    self::ep($baseUrl, 'POST', 'server-4/price', 'Quote price', [
                        'country' => '12',
                        'service' => 'wa',
                    ], [
                        'success' => true,
                        'country' => '12',
                        'service' => 'wa',
                        'usd' => 0.52,
                        'price' => 1750.0,
                        'available' => 30,
                    ]),
                    self::ep($baseUrl, 'POST', 'server-4/rent', 'Buy number', [
                        'country' => '12',
                        'service' => 'wa',
                    ], [
                        'success' => true,
                        'server' => 'Server 4',
                        'order_id' => 12348,
                        'phone' => '447700900456',
                        'service' => 'wa',
                        'country' => '12',
                        'price' => 1750.0,
                        'provider_order_id' => 'sv3-xyz',
                    ]),
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $body
     * @param  array<string, mixed>  $response
     * @return array<string, mixed>
     */
    protected static function ep(
        string $baseUrl,
        string $method,
        string $path,
        string $title,
        ?array $body,
        array $response,
        ?string $notes = null,
    ): array {
        return [
            'method' => $method,
            'path' => $path,
            'title' => $title,
            'url' => $baseUrl.'/'.$path,
            'body' => $body,
            'body_json' => $body !== null
                ? json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : null,
            'curl' => self::curl($baseUrl, $method, $path, $body),
            'response' => json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'notes' => $notes,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $body
     */
    protected static function curl(string $baseUrl, string $method, string $path, ?array $body): string
    {
        $url = $baseUrl.'/'.$path;
        $httpMethod = str_contains($method, 'POST') ? 'POST' : 'GET';

        $lines = [
            'curl -X '.$httpMethod.' "'.$url.'" \\',
            '  -H "Authorization: Bearer YOUR_API_KEY" \\',
            '  -H "Accept: application/json"',
        ];

        if ($body !== null && $httpMethod === 'POST') {
            $lines[2] .= ' \\';
            $lines[] = '  -H "Content-Type: application/json" \\';
            $lines[] = "  -d '".json_encode($body, JSON_UNESCAPED_SLASHES)."'";
        }

        return implode("\n", $lines);
    }
}
