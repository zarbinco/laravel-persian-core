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
            'normalize_teh_marbuta' => true,
        ],
    ],

    'numbers' => [
        'storage_digits' => 'en',
        'display_digits' => 'fa',
    ],
];
