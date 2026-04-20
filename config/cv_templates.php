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
        'default' => [
            'name' => 'Default',
            'view' => 'templates.default',
            'is_default' => true,
            'is_active' => true,
        ],
        'modern' => [
            'name' => 'Modern',
            'view' => 'templates.modern',
            'is_default' => false,
            'is_active' => true,
        ],
        'minimal' => [
            'name' => 'Minimal',
            'view' => 'templates.minimalist',
            'is_default' => false,
            'is_active' => true,
        ],
        'classic' => [
            'name' => 'Classic',
            'view' => 'templates.classic',
            'is_default' => false,
            'is_active' => true,
        ],
        'sidebar' => [
            'name' => 'Sidebar',
            'view' => 'templates.sidebar',
            'is_default' => false,
            'is_active' => true,
        ],
        'mono-poster' => [
            'name' => 'Mono Poster',
            'view' => 'templates.mono-poster',
            'is_default' => false,
            'is_active' => true,
        ],
        'creative' => [
            'name' => 'Creative',
            'view' => 'templates.creative',
            'is_default' => false,
            'is_active' => true,
        ],
        'ats' => [
            'name' => 'ATS',
            'view' => 'templates.ats',
            'is_default' => false,
            'is_active' => true,
        ],
        'timeline' => [
            'name' => 'Timeline',
            'view' => 'templates.timeline',
            'is_default' => false,
            'is_active' => true,
        ],
        'profile-sidebar' => [
            'name' => 'Profile Sidebar',
            'view' => 'templates.profile-sidebar',
            'is_default' => false,
            'is_active' => true,
        ],
    ],
];
