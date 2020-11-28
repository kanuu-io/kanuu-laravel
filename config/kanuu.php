<?php

return [
    'api_key' => env('KANUU_API_KEY'),
    'base_url' => env('KANUU_BASE_URL', 'https://kanuu.io'),

    'providers' => [
        'paddle' => [
            'public_key' => env('PADDLE_PUBLIC_KEY'),
        ],
    ],

    'user_model' => \App\Models\User::class,
];
