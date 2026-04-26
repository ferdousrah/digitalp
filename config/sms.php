<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default SMS driver
    |--------------------------------------------------------------------------
    |
    | Supported: "log", "bulksmsbd"
    | The "log" driver writes the SMS to laravel.log — useful for development.
    |
    */
    'driver' => env('SMS_DRIVER', 'log'),

    'bulksmsbd' => [
        'api_key'   => env('BULKSMSBD_API_KEY'),
        'sender_id' => env('BULKSMSBD_SENDER_ID'),
        'endpoint'  => env('BULKSMSBD_ENDPOINT', 'https://bulksmsbd.net/api/smsapi'),
    ],

    'otp' => [
        'length'         => 6,
        'ttl_minutes'    => 5,
        'max_attempts'   => 5,   // verify attempts per code
        'max_per_window' => 3,   // codes issued per phone per window
        'window_minutes' => 10,
    ],
];
