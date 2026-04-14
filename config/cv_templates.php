<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CV Templates
    |--------------------------------------------------------------------------
    |
    | Define the available CV templates.
    |
    */

    'templates' => [
        'minimalist' => [
            'name' => 'Minimalist',
            'view' => 'cv.templates.minimalist',
            'is_default' => true,
            'is_active' => true,
        ],
        'modern' => [
            'name' => 'Modern',
            'view' => 'cv.templates.modern',
            'is_default' => false,
            'is_active' => true,
        ],
        'creative' => [
            'name' => 'Creative',
            'view' => 'cv.templates.creative',
            'is_default' => false,
            'is_active' => true,
        ],
    ],
];
