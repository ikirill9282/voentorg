<?php

return [
    'merchant_login' => env('VTB_MERCHANT_LOGIN', ''),
    'merchant_password' => env('VTB_MERCHANT_PASSWORD', ''),
    'api_url' => env('VTB_API_URL', 'https://vtb4bill-test.cft.ru/payment/rest/'),
    'return_url' => env('VTB_RETURN_URL', ''),
    'fail_url' => env('VTB_FAIL_URL', ''),
];
