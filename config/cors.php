<?php

return [

    'paths' => ['api/*', 'login', 'register', '*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // 或指定 ['http://172.23.50.20:8100']

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
