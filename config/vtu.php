<?php

return [
    'categories' => [
        'airtime' => env('VTU_CAT_AIRTIME'),
        'data' => env('VTU_CAT_DATA'),
        'cable_tv' => env('VTU_CAT_CABLE_TV'),
        'electricity' => env('VTU_CAT_ELECTRICITY'),
    ],

    'networks' => [
        ['id' => 'mtn', 'name' => 'MTN'],
        ['id' => 'glo', 'name' => 'Glo'],
        ['id' => 'airtel', 'name' => 'Airtel'],
        ['id' => '9mobile', 'name' => '9mobile'],
    ],

    'discos' => [
        ['id' => 'ikeja-electric', 'name' => 'Ikeja Electric'],
        ['id' => 'eko-electric', 'name' => 'Eko Electric'],
        ['id' => 'kano-electric', 'name' => 'Kano Electric'],
        ['id' => 'portharcourt-electric', 'name' => 'Port Harcourt Electric'],
        ['id' => 'enugu-electric', 'name' => 'Enugu Electric'],
        ['id' => 'ibadan-electric', 'name' => 'Ibadan Electric'],
        ['id' => 'kaduna-electric', 'name' => 'Kaduna Electric'],
        ['id' => 'abuja-electric', 'name' => 'Abuja Electric'],
        ['id' => 'jos-electric', 'name' => 'Jos Electric'],
        ['id' => 'benin-electric', 'name' => 'Benin Electric'],
        ['id' => 'yola-electric', 'name' => 'Yola Electric'],
        ['id' => 'maiduguri-electric', 'name' => 'Maiduguri Electric'],
    ],

    'airtime' => [
        'min_amount' => 50,
        'max_amount' => 100000,
        'airtel_max' => 10000,
    ],

    'data' => [
        'min_amount' => 1,
        'max_amount' => 500000,
    ],

    'cable' => [
        'min_amount' => 1,
    ],

    'electricity' => [
        'min_amount' => 100,
        'max_amount' => 500000,
    ],
];
