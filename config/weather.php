<?php

return [
   'openweather' => [
        'key' => env('OPENWEATHER_API_KEY'),
        'verify_ssl' => env('WEATHER_VERIFY_SSL', true),
    ],
];
