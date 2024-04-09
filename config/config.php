<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'default' => env('SBQ_DEFAULT_CONNECITON', 'starbust'),
    'connections' => [
        'starbust' => [
            'host' => env('SBQ_HOST'),
            'port' => env('SBQ_PORT'),
            'user' => env('SBQ_USER'),
            'password' => env('SBQ_PASSWORD'),
            'catalog' => env('SBQ_CATALOG'),
            'schema' => env('SBQ_SCHEMA'),
            'driver' => env('SBQ_DRIVER', 'Starburst ODBC Driver'),
        ]
    ]
];
