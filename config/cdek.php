<?php

return [
    'client_id' => env('CDEK_CLIENT_ID', ''),
    'client_secret' => env('CDEK_CLIENT_SECRET', ''),
    'api_url' => env('CDEK_API_URL', 'https://api.edu.cdek.ru/v2'),
    'sender_city_code' => env('CDEK_SENDER_CITY_CODE', '44'), // Moscow
];
