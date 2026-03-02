<?php

return [
    'service_account_json' => env('GOOGLE_SERVICE_ACCOUNT_JSON', storage_path('app/google-credentials.json')),
    'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID', '1pbLzwZURrU3-QeHwjcmbC6WJspvtjiMQbP7brdk6QFQ'),
    'sheet_name' => env('GOOGLE_SHEETS_SHEET_NAME', 'Ядро сайта и прайслиста'),
    'header_row' => 5,
    'data_start_row' => 6,
];
