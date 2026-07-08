<?php

return [
    'text' => [
        'normalize_arabic_yeh' => true,
        'normalize_arabic_kaf' => true,
        'remove_diacritics' => true,
        'remove_tatweel' => true,
        'normalize_whitespace' => true,
        'remove_invisible_characters' => true,

        'display' => [
            'normalize_ellipsis' => true,
            'normalize_punctuation_spacing' => true,
        ],

        'search' => [
            'zwnj' => 'space',
            'remove_punctuation' => true,
            'normalize_arabic_alef' => true,
            'normalize_madda_alef' => true,
            'normalize_teh_marbuta' => true,
        ],
    ],

    'numbers' => [
        'storage_digits' => 'en',
        'display_digits' => 'fa',
        'thousands_separator' => ',',
        'decimal_separator' => '.',
    ],

    'mobile' => [
        'default_country' => 'IR',
        'iran' => [
            'country_code' => '98',
            'national_prefix' => '0',
            'mask_pattern' => '0912***4567',
        ],
    ],

    'money' => [
        'default_currency' => 'toman',
        'display_digits' => 'fa',
        'thousands_separator' => ',',
        'rial_to_toman_rate' => 10,

        'labels' => [
            'fa' => [
                'toman' => 'تومان',
                'rial' => 'ریال',
            ],
            'en' => [
                'toman' => 'toman',
                'rial' => 'rial',
            ],
        ],
    ],

    'banks' => [
        'unknown_returns_null' => true,
    ],

    'bank_data' => [
        'version' => '2026-06-26',
        'source' => 'manual-curated',
        'strict_unknown' => false,
    ],

    'validation' => [
        'strict' => true,
        'empty_values_pass' => true,

        'iranian_mobile' => [
            'strict_operator_prefixes' => false,
        ],

        'iranian_postal_code' => [
            'reject_repeated_digits' => true,
        ],

        'iranian_card_number' => [
            'require_luhn' => true,
            'require_iranian_bin' => false,
        ],
    ],

    'developer_experience' => [
        'str_macros' => [
            'enabled' => false,
        ],
    ],
];
