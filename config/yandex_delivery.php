<?php

return [
    'token' => env('YANDEX_DELIVERY_TOKEN', ''),
    'api_url' => env('YANDEX_DELIVERY_API_URL', 'https://b2b.taxi.yandex.net'),
    'sender_id' => env('YANDEX_DELIVERY_SENDER_ID', ''),
    'sender_city' => env('YANDEX_DELIVERY_SENDER_CITY', 'Москва'),
    'sender_address' => env('YANDEX_DELIVERY_SENDER_ADDRESS', 'Москва, Остаповский проезд, дом 5, строение 10'),
    'sender_phone' => env('YANDEX_DELIVERY_SENDER_PHONE', '+74998880701'),
];
