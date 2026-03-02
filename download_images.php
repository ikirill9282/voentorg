<?php
/**
 * Download product/post images from colchuga.ru and update DB paths
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Create directories
$dirs = ['products', 'posts', 'categories'];
foreach ($dirs as $dir) {
    $path = storage_path("app/public/$dir");
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "Created: $path\n";
    }
}

// Download function
function downloadImage(string $url, string $savePath): bool
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible)',
    ]);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $data && strlen($data) > 100) {
        file_put_contents($savePath, $data);
        return true;
    }
    return false;
}

// 1. Download product images
echo "\n=== Downloading product images ===\n";
$images = DB::table('product_images')
    ->where('path', 'like', 'https://%')
    ->get();

$downloaded = 0;
$failed = 0;
$total = $images->count();

foreach ($images as $i => $img) {
    $url = $img->path;
    $filename = basename(parse_url($url, PHP_URL_PATH));
    // Ensure unique filename
    $filename = $img->product_id . '_' . $filename;
    $localPath = storage_path("app/public/products/$filename");
    $dbPath = "/storage/products/$filename";

    if (file_exists($localPath)) {
        // Already downloaded, just update DB
        DB::table('product_images')->where('id', $img->id)->update(['path' => $dbPath]);
        $downloaded++;
        continue;
    }

    if (downloadImage($url, $localPath)) {
        DB::table('product_images')->where('id', $img->id)->update(['path' => $dbPath]);
        $downloaded++;
    } else {
        // Set a placeholder path
        DB::table('product_images')->where('id', $img->id)->update(['path' => $dbPath]);
        $failed++;
    }

    if (($i + 1) % 20 === 0 || $i === $total - 1) {
        echo "  Progress: " . ($i + 1) . "/$total (downloaded: $downloaded, failed: $failed)\n";
    }
}
echo "Product images: $downloaded downloaded, $failed failed out of $total\n";

// 2. Download post featured images
echo "\n=== Downloading post images ===\n";
$posts = DB::table('posts')
    ->whereNotNull('featured_image')
    ->where('featured_image', 'like', 'https://%')
    ->get();

foreach ($posts as $post) {
    $url = $post->featured_image;
    $filename = basename(parse_url($url, PHP_URL_PATH));
    $filename = $post->id . '_' . $filename;
    $localPath = storage_path("app/public/posts/$filename");
    $dbPath = "/storage/posts/$filename";

    if (file_exists($localPath) || downloadImage($url, $localPath)) {
        DB::table('posts')->where('id', $post->id)->update(['featured_image' => $dbPath]);
        echo "  Post #{$post->id}: OK\n";
    } else {
        DB::table('posts')->where('id', $post->id)->update(['featured_image' => $dbPath]);
        echo "  Post #{$post->id}: FAILED\n";
    }
}

// 3. Create storage symlink
echo "\n=== Creating storage symlink ===\n";
$publicStorage = public_path('storage');
$targetPath = storage_path('app/public');

if (!file_exists($publicStorage)) {
    symlink($targetPath, $publicStorage);
    echo "Symlink created: $publicStorage -> $targetPath\n";
} else {
    echo "Symlink already exists: $publicStorage\n";
}

echo "\nDone!\n";
