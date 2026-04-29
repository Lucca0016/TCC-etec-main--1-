<?php

return [
    'secret' => env('JWT_SECRET', env('APP_KEY', 'change-this-secret')),
    'issuer' => env('JWT_ISSUER', env('APP_URL', 'http://localhost')),
    'access_ttl' => 900,
    'refresh_ttl' => 604800,
];
