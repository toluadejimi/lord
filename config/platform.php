<?php

return [
    'config_groups' => [
        'sprintpay' => [
            'label' => 'SprintPay (Wallet & VTU)',
            'keys' => [
                'WEBKEY' => ['label' => 'Merchant Web Key', 'type' => 'password', 'env' => 'WEBKEY'],
                'SPRINTPAY_WEBHOOK_SECRET' => ['label' => 'Webhook Bearer Secret', 'type' => 'password', 'env' => 'SPRINTPAY_WEBHOOK_SECRET'],
                'SPRINTPAY_API_BASE' => ['label' => 'API Base URL', 'type' => 'text', 'env' => 'SPRINTPAY_API_BASE', 'default' => 'https://web.sprintpay.online/api'],
                'PALMPAYKEY' => ['label' => 'PalmPay Key', 'type' => 'password', 'env' => 'PALMPAYKEY'],
            ],
        ],
        'smspool' => [
            'label' => 'SMSPool — World API',
            'keys' => [
                'provider_smspool_enabled' => ['label' => 'Enabled', 'type' => 'boolean', 'env' => null, 'default' => '1'],
                'WKEY' => ['label' => 'API Key', 'type' => 'password', 'env' => 'WKEY'],
            ],
        ],
        'usa2' => [
            'label' => 'Server 2 — Unlimited Portal',
            'keys' => [
                'provider_usa2_enabled' => ['label' => 'Enabled', 'type' => 'boolean', 'env' => null, 'default' => '0'],
                'UNLIMITED_API_KEY' => ['label' => 'API Key', 'type' => 'password', 'env' => 'UNLIMITED_API_KEY'],
                'UNLIMITED_USER' => ['label' => 'Portal Username', 'type' => 'text', 'env' => 'UNLIMITED_USER'],
                'TRUVER_API_KEY' => ['label' => 'TruVerifi API Key (optional)', 'type' => 'password', 'env' => 'TRUVER_API_KEY'],
            ],
        ],
        'hero' => [
            'label' => 'Server 3 — HeroSMS',
            'keys' => [
                'provider_hero_enabled' => ['label' => 'Enabled', 'type' => 'boolean', 'env' => null, 'default' => '0'],
                'SMS_SERVER_HERO_API_KEY' => ['label' => 'API Key', 'type' => 'password', 'env' => 'SMS_SERVER_HERO_API_KEY'],
                'SMS_SERVER_HERO_BASE_URL' => ['label' => 'Base URL', 'type' => 'text', 'env' => 'SMS_SERVER_HERO_BASE_URL', 'default' => 'https://hero-sms.com'],
            ],
        ],
        'sv3' => [
            'label' => 'Server 4 — SMS Bower',
            'keys' => [
                'provider_sv3_enabled' => ['label' => 'Enabled', 'type' => 'boolean', 'env' => null, 'default' => '0'],
                'SMS_SERVER_WORLD_SV3_API_KEY' => ['label' => 'API Key', 'type' => 'password', 'env' => 'SMS_SERVER_WORLD_SV3_API_KEY'],
                'SMS_SERVER_WORLD_SV3_BASE_URL' => ['label' => 'Base URL', 'type' => 'text', 'env' => 'SMS_SERVER_WORLD_SV3_BASE_URL', 'default' => 'https://smsbower.page'],
            ],
        ],
        'usa1' => [
            'label' => 'USA Server 1 — Legacy Handler (Retired)',
            'keys' => [
                'provider_usa1_enabled' => ['label' => 'Enabled', 'type' => 'boolean', 'env' => null, 'default' => '0'],
                'KEY' => ['label' => 'Legacy API Key', 'type' => 'password', 'env' => 'KEY'],
            ],
        ],
        'sim' => [
            'label' => 'Server 1 — 5SIM',
            'keys' => [
                'provider_sim_enabled' => ['label' => 'Enabled', 'type' => 'boolean', 'env' => null, 'default' => '1'],
                'SIMTOKEN' => ['label' => 'Bearer Token', 'type' => 'password', 'env' => 'SIMTOKEN'],
            ],
        ],
        'security' => [
            'label' => 'Security & Turnstile',
            'keys' => [
                'TURNSTILE_SITE_KEY' => ['label' => 'Turnstile Site Key', 'type' => 'text', 'env' => 'TURNSTILE_SITE_KEY'],
                'TURNSTILE_SITE_SECRET' => ['label' => 'Turnstile Secret', 'type' => 'password', 'env' => 'TURNSTILE_SITE_SECRET'],
                'LANDING_TURNSTILE_GATE' => ['label' => 'Landing Gate Enabled', 'type' => 'boolean', 'env' => 'LANDING_TURNSTILE_GATE', 'default' => '0'],
                'WEBHOOK_INBOUND_SECRET' => ['label' => 'Inbound Webhook Secret', 'type' => 'password', 'env' => 'WEBHOOK_INBOUND_SECRET'],
                'IPA' => ['label' => 'Allowed IP (e_fund A)', 'type' => 'text', 'env' => 'IPA'],
                'IPB' => ['label' => 'Allowed IP (e_fund B)', 'type' => 'text', 'env' => 'IPB'],
            ],
        ],
        'telegram' => [
            'label' => 'Telegram Notifications',
            'keys' => [
                'TELEGRAM_BOT_TOKEN' => ['label' => 'Bot Token', 'type' => 'password', 'env' => 'TELEGRAM_BOT_TOKEN'],
                'TELEGRAM_ADMIN_CHAT_ID' => ['label' => 'Admin Chat ID', 'type' => 'text', 'env' => 'TELEGRAM_ADMIN_CHAT_ID'],
                'TELEGRAM_BOT_TOKEN_2' => ['label' => 'Secondary Bot Token', 'type' => 'password', 'env' => 'TELEGRAM_BOT_TOKEN_2'],
                'TELEGRAM_CHAT_ID_2' => ['label' => 'Secondary Chat ID', 'type' => 'text', 'env' => 'TELEGRAM_CHAT_ID_2'],
            ],
        ],
        'vtu' => [
            'label' => 'VTU Category IDs (SprintPay VAS)',
            'keys' => [
                'provider_vtu_enabled' => ['label' => 'VTU Module Enabled', 'type' => 'boolean', 'env' => null, 'default' => '1'],
                'VTU_CAT_AIRTIME' => ['label' => 'Airtime Category ID', 'type' => 'text', 'env' => 'VTU_CAT_AIRTIME'],
                'VTU_CAT_DATA' => ['label' => 'Data Category ID', 'type' => 'text', 'env' => 'VTU_CAT_DATA'],
                'VTU_CAT_CABLE_TV' => ['label' => 'Cable TV Category ID', 'type' => 'text', 'env' => 'VTU_CAT_CABLE_TV'],
                'VTU_CAT_ELECTRICITY' => ['label' => 'Electricity Category ID', 'type' => 'text', 'env' => 'VTU_CAT_ELECTRICITY'],
                'vtu_airtime_enabled' => ['label' => 'Airtime Enabled', 'type' => 'boolean', 'env' => null, 'default' => '1'],
                'vtu_data_enabled' => ['label' => 'Data Enabled', 'type' => 'boolean', 'env' => null, 'default' => '1'],
                'vtu_cable_enabled' => ['label' => 'Cable Enabled', 'type' => 'boolean', 'env' => null, 'default' => '1'],
                'vtu_electricity_enabled' => ['label' => 'Electricity Enabled', 'type' => 'boolean', 'env' => null, 'default' => '1'],
            ],
        ],
        'telegram_blue_tick' => [
            'label' => 'Telegram Blue Tick (iStar Premium)',
            'keys' => [
                'provider_telegram_blue_tick_enabled' => ['label' => 'Module Enabled', 'type' => 'boolean', 'env' => null, 'default' => '0'],
                'ISTAR_API_KEY' => ['label' => 'iStar API Key', 'type' => 'password', 'env' => 'ISTAR_API_KEY'],
                'ISTAR_API_BASE' => ['label' => 'API Base URL', 'type' => 'text', 'env' => 'ISTAR_API_BASE', 'default' => 'https://v1.fragmentapi.com/api/v1/partner'],
                'ISTAR_WEBHOOK_SECRET' => ['label' => 'Webhook HMAC Secret', 'type' => 'password', 'env' => 'ISTAR_WEBHOOK_SECRET'],
                'telegram_premium_price_3' => ['label' => 'Fixed price 3 mo (NGN)', 'type' => 'text', 'env' => null],
                'telegram_premium_price_6' => ['label' => 'Fixed price 6 mo (NGN)', 'type' => 'text', 'env' => null],
                'telegram_premium_price_12' => ['label' => 'Fixed price 12 mo (NGN)', 'type' => 'text', 'env' => null],
            ],
        ],
        'site' => [
            'label' => 'Sitewide',
            'keys' => [
                'site_notification_title' => ['label' => 'Banner Title', 'type' => 'text', 'env' => null],
                'site_notification_message' => ['label' => 'Banner Message', 'type' => 'textarea', 'env' => null],
                'site_notification_active' => ['label' => 'Banner Active', 'type' => 'boolean', 'env' => null, 'default' => '0'],
            ],
        ],
    ],

    'setting_rows' => [
        1 => ['name' => 'usa1_api', 'label' => 'USA1 + API World Pricing'],
        2 => ['name' => 'smspool', 'label' => 'SMSPool World API'],
        3 => ['name' => 'sim', 'label' => 'Server 1 — 5SIM'],
        4 => ['name' => 'usa2', 'label' => 'Server 2 — Unlimited Portal'],
        5 => ['name' => 'hero', 'label' => 'Server 3 — HeroSMS'],
        6 => ['name' => 'sv3', 'label' => 'Server 4 — SMS Bower'],
        7 => ['name' => 'telegram_premium', 'label' => 'Telegram Blue Tick'],
    ],

    'verification_types' => [
        'usa1' => 1,
        'world_legacy' => 2,
        'sim' => 3,
        'usa2' => 4,
        'smspool' => 8,
        'hero' => 9,
        'sv3' => 10,
    ],

    'admin_settings_groups' => ['sprintpay', 'security', 'telegram', 'site'],

    'admin_service_groups' => [
        'sim' => [
            'setting_id' => 3,
            'icon' => 'fa-globe',
            'menu_label' => 'Server 1',
            'provider' => '5SIM',
            'user_route' => '/cworld',
            'description' => 'All-countries SMS verification panel (user menu: Server 1).',
        ],
        'usa2' => [
            'setting_id' => 4,
            'icon' => 'fa-flag-usa',
            'menu_label' => 'Server 2',
            'provider' => 'Unlimited Portal',
            'user_route' => '/usa2',
            'description' => 'US number rentals via Unlimited Portal (user menu: Server 2).',
        ],
        'hero' => [
            'setting_id' => 5,
            'icon' => 'fa-server',
            'menu_label' => 'Server 3',
            'provider' => 'HeroSMS',
            'user_route' => '/world-sv2',
            'description' => 'International verification via HeroSMS (user menu: Server 3).',
        ],
        'sv3' => [
            'setting_id' => 6,
            'icon' => 'fa-layer-group',
            'menu_label' => 'Server 4',
            'provider' => 'SMS Bower',
            'user_route' => '/world-sv3',
            'description' => 'International verification via SMS Bower (user menu: Server 4).',
        ],
        'smspool' => [
            'setting_id' => 2,
            'icon' => 'fa-sms',
            'menu_label' => 'SMSPool API',
            'provider' => 'SMSPool',
            'user_route' => null,
            'description' => 'Backend world API integration (SMSPool). Not shown as a separate user menu item.',
        ],
        'usa1' => [
            'setting_id' => 1,
            'icon' => 'fa-ban',
            'menu_label' => 'Legacy USA',
            'provider' => 'DaisySMS / Handler',
            'user_route' => null,
            'description' => 'Retired USA Server 1 legacy handler.',
        ],
    ],

    'admin_vtu_services' => [
        'airtime' => [
            'label' => 'Airtime',
            'description' => 'Instant mobile top-up for all Nigerian networks.',
            'icon' => 'ti-phone',
            'category_key' => 'VTU_CAT_AIRTIME',
            'enabled_key' => 'vtu_airtime_enabled',
        ],
        'data' => [
            'label' => 'Data Bundles',
            'description' => 'Buy SME, daily, weekly, and monthly data plans.',
            'icon' => 'ti-wifi',
            'category_key' => 'VTU_CAT_DATA',
            'enabled_key' => 'vtu_data_enabled',
        ],
        'cable' => [
            'label' => 'Cable TV',
            'description' => 'Renew DSTV, GOTV, Startimes, and other TV subscriptions.',
            'icon' => 'ti-device-tv',
            'category_key' => 'VTU_CAT_CABLE_TV',
            'enabled_key' => 'vtu_cable_enabled',
        ],
        'electricity' => [
            'label' => 'Electricity',
            'description' => 'Pay prepaid and postpaid electricity bills.',
            'icon' => 'ti-bolt',
            'category_key' => 'VTU_CAT_ELECTRICITY',
            'enabled_key' => 'vtu_electricity_enabled',
        ],
    ],
];
