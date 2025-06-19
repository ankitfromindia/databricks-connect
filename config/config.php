<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'default' => env('DBX_DEFAULT_CONNECITON', 'databricks'),
    'connections' => [
        'databricks' => [
            'driver' => 'Databricks ODBC Driver',
            'host' => env('DATABRICKS_HOST'),
            'path' => env('DATABRICKS_PATH'),
            'token' => env('DATABRICKS_TOKEN'),
            'aws_key' => env('AWS_ACCESS_KEY_ID'),
            'aws_secret' => env('AWS_SECRET_ACCESS_KEY'),
            'charset' => env('DATABRICKS_CHARSET', 'UTF-8'),
        ]
    ]
];
