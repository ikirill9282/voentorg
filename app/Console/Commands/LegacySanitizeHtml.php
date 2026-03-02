<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LegacySanitizeHtml extends Command
{
    protected $signature = 'legacy:sanitize-html {--source=} {--output=}';

    protected $description = 'Sanitize legacy HTML files for import into Laravel views and database';

    public function handle(): int
    {
        $sourceDir = $this->option('source') ?: storage_path('app/legacy/source');
        $outputDir = $this->option('output') ?: storage_path('app/legacy/sanitized');

        if (! File::isDirectory($sourceDir)) {
            $this->error("Source directory not found: {$sourceDir}");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($outputDir);

        $files = collect(File::files($sourceDir))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'html')
            ->values();

        if ($files->isEmpty()) {
            $this->warn("No HTML files found in {$sourceDir}");

            return self::SUCCESS;
        }

        foreach ($files as $file) {
            $html = File::get($file->getPathname());
            $sanitized = $this->sanitizeHtml($html);

            File::put($outputDir.DIRECTORY_SEPARATOR.$file->getFilename(), $sanitized);
        }

        $this->info("Sanitized {$files->count()} HTML files into {$outputDir}");

        return self::SUCCESS;
    }

    private function sanitizeHtml(string $html): string
    {
        $sanitized = $html;

        $attributeConversions = [
            'data-savepage-href' => 'href',
            'data-savepage-src' => 'src',
            'data-savepage-srcset' => 'srcset',
            'data-savepage-type' => 'type',
        ];

        foreach ($attributeConversions as $from => $to) {
            $sanitized = preg_replace('/\s'.preg_quote($from, '/').'=/iu', ' '.$to.'=', $sanitized) ?? $sanitized;
        }

        $removePatterns = [
            '/<script[^>]*type=("|\')text\/plain\1[^>]*>.*?<\/script>/is',
            '/<div[^>]*id=("|\')wpadminbar\1[^>]*>.*?<\/div>/is',
            '/<div[^>]*id=("|\')query-monitor-main\1[^>]*>.*?<\/div>/is',
            '/<div[^>]*id=("|\')qmwrapper\1[^>]*>.*?<\/div>/is',
            '/<style[^>]*>.*?(#wpadminbar|#query-monitor-main|\.qm\-).*?<\/style>/is',
            '/<script[^>]*(query-monitor|wordfence|wp-admin-bar|wpadminbar)[^>]*>.*?<\/script>/is',
        ];

        foreach ($removePatterns as $pattern) {
            $sanitized = preg_replace($pattern, '', $sanitized) ?? $sanitized;
        }

        $sanitized = preg_replace('/\sdata-savepage-[a-z0-9_\-]+=("[^"]*"|\'[^\']*\')/iu', '', $sanitized) ?? $sanitized;

        return $sanitized;
    }
}
