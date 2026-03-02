<?php
/**
 * Download category images and update the DB
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$categories = [
    'komplekti' => 'https://colchuga.ru/wp-content/uploads/2025/02/2.-komplekty-snaryazheniya-1.jpg',
    'balistic-zashita' => 'https://colchuga.ru/wp-content/uploads/2025/02/3.-ballisticheskaya-zashhita-1.jpg',
    'takticheskie-poyasa' => 'https://colchuga.ru/wp-content/uploads/2025/02/4.-takticheskij-poyas.jpg',
    'broneshlemy' => 'https://colchuga.ru/wp-content/uploads/2024/08/card-8.jpg',
    'brone-zhilety' => 'https://colchuga.ru/wp-content/uploads/2025/02/1.-bronezhilety-2.jpg',
    'podsumki' => 'https://colchuga.ru/wp-content/uploads/2024/08/card-4.jpg',
    'odezhda' => 'https://colchuga.ru/wp-content/uploads/2024/08/card-7.jpg',
    'ryukzaki-i-sumki' => 'https://colchuga.ru/wp-content/uploads/2025/02/ryukzaki-i-sumki-1.jpg',
    'aksessuary' => 'https://colchuga.ru/wp-content/uploads/2024/08/card-6.jpg',
    'strazh-edtition' => 'https://colchuga.ru/wp-content/uploads/2025/04/veb-1.png',
];

$dir = storage_path('app/public/categories');
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

foreach ($categories as $slug => $url) {
    $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
    $filename = $slug . '.' . $ext;
    $localPath = "$dir/$filename";
    $dbPath = "/storage/categories/$filename";

    if (!file_exists($localPath)) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0',
        ]);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && strlen($data) > 100) {
            file_put_contents($localPath, $data);
            echo "Downloaded: $slug\n";
        } else {
            echo "FAILED: $slug (HTTP $code)\n";
            continue;
        }
    } else {
        echo "EXISTS: $slug\n";
    }

    $updated = DB::table('categories')->where('slug', $slug)->update(['image' => $dbPath]);
    echo "  DB updated: $updated rows\n";
}

echo "\nDone!\n";
