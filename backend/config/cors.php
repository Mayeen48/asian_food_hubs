<?php

$localOrigins = [
    'http://localhost:5173',
    'http://127.0.0.1:5173',
    // allow any device connected to local WiFi
    'http://192.168.0.0/16',
];

$productionOrigins = [
    'https://asianfoodhubs.com',
    'https://www.asianfoodhubs.com',
    'https://api.asianfoodhubs.com',
];
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => env('APP_ENV') === 'local'
        ? $localOrigins
        : $productionOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
