<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadImages extends Command
{
    protected $signature = 'images:download {--dry-run : Show what would be downloaded without actually downloading}';
    protected $description = 'Download product images from external URLs to local storage';

    public function handle(): int
    {
        $images = ProductImage::where('path', 'like', 'https://%')->get();

        $this->info("Found {$images->count()} external images to download.");

        if ($this->option('dry-run')) {
            $images->each(fn ($img) => $this->line("  [{$img->product_id}] {$img->path}"));
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($images as $image) {
            try {
                $response = Http::timeout(30)->get($image->path);

                if (! $response->successful()) {
                    $this->newLine();
                    $this->warn("  Failed ({$response->status()}): {$image->path}");
                    $failed++;
                    $bar->advance();
                    continue;
                }

                $urlPath = parse_url($image->path, PHP_URL_PATH);
                $filename = basename($urlPath);
                $storagePath = "products/{$image->product_id}/{$filename}";

                Storage::disk('public')->put($storagePath, $response->body());

                $image->update(['path' => $storagePath]);
                $success++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("  Error: {$image->path} — {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Downloaded: {$success}, Failed: {$failed}");

        return self::SUCCESS;
    }
}
