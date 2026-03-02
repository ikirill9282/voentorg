<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LegacyFetchAssets extends Command
{
    protected $signature = 'legacy:fetch-assets {--source=} {--public=} {--allow-missing : Do not fail when some assets cannot be downloaded} {--max-assets=300}';

    protected $description = 'Download external legacy assets locally into public/legacy and rewrite sanitized HTML';

    public function handle(): int
    {
        $sourceDir = $this->option('source') ?: storage_path('app/legacy/sanitized');
        $publicRoot = $this->option('public') ?: public_path('legacy/assets');
        $maxAssets = max(1, (int) $this->option('max-assets'));

        if (! File::isDirectory($sourceDir)) {
            $this->error("Source directory not found: {$sourceDir}");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($publicRoot);

        $files = collect(File::files($sourceDir))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'html')
            ->values();

        if ($files->isEmpty()) {
            $this->warn("No HTML files found in {$sourceDir}");

            return self::SUCCESS;
        }

        $allUrls = [];
        $htmlByFile = [];

        foreach ($files as $file) {
            $html = File::get($file->getPathname());
            $htmlByFile[$file->getPathname()] = $html;

            foreach ($this->extractUrls($html) as $url) {
                if (! $this->isCandidateAssetUrl($url)) {
                    continue;
                }

                $allUrls[$url] = true;
            }
        }

        $urls = array_keys($allUrls);
        sort($urls);

        $isLimited = count($urls) > $maxAssets;
        if ($isLimited) {
            $this->warn('Found '.count($urls).' candidate URLs, processing first '.$maxAssets.' (use --max-assets to increase).');
            $urls = array_slice($urls, 0, $maxAssets);
        }

        $urlMap = [];
        $missing = [];
        $downloaded = 0;
        $reused = 0;

        foreach ($urls as $index => $url) {
            $this->line('['.($index + 1).'/'.count($urls).'] '.$url);

            $resolved = $this->downloadAsset($url);

            if (! $resolved['ok']) {
                $missing[] = [
                    'url' => $url,
                    'error' => $resolved['error'],
                ];
                continue;
            }

            $urlMap[$url] = $resolved['local'];
            $downloaded += $resolved['downloaded'] ? 1 : 0;
            $reused += $resolved['downloaded'] ? 0 : 1;
        }

        foreach ($htmlByFile as $path => $html) {
            File::put($path, $this->rewriteHtml($html, $urlMap));
        }

        $mapPath = storage_path('app/legacy/asset-map.json');
        File::ensureDirectoryExists(dirname($mapPath));
        File::put(
            $mapPath,
            json_encode($urlMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        $this->info('Assets map written to '.$mapPath);
        $this->line("Assets resolved: {$downloaded} downloaded, {$reused} reused");
        $this->line('URLs rewritten: '.count($urlMap));

        if (! empty($missing)) {
            $this->warn('Missing assets: '.count($missing));

            foreach (array_slice($missing, 0, 30) as $row) {
                $this->line("- {$row['url']} ({$row['error']})");
            }

            if (! $this->option('allow-missing')) {
                $this->error('Critical missing assets found. Re-run with --allow-missing to skip failure.');

                return self::FAILURE;
            }
        }

        if ($isLimited) {
            $this->warn('Processing was limited by --max-assets. Increase limit for full download pass.');
        }

        return self::SUCCESS;
    }

    private function extractUrls(string $html): array
    {
        $urls = [];

        $patterns = [
            '/\b(?:src|href)=["\'](https?:\/\/[^"\']+)["\']/iu',
            '/\/\*\s*savepage-url=([^*\s]+)\s*\*\//iu',
            '/url\((["\']?)(https?:\/\/[^)"\']+)\1\)/iu',
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match_all($pattern, $html, $matches)) {
                continue;
            }

            $candidateList = $matches[2] ?? $matches[1] ?? [];

            foreach ($candidateList as $candidate) {
                $candidate = html_entity_decode(trim($candidate), ENT_QUOTES | ENT_HTML5);
                if (! Str::startsWith($candidate, ['http://', 'https://'])) {
                    continue;
                }
                $urls[$candidate] = true;
            }
        }

        return array_keys($urls);
    }

    private function isCandidateAssetUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $allowedHosts = [
            'colchuga.ru',
            'www.colchuga.ru',
            'cdn.jsdelivr.net',
            'cdnjs.cloudflare.com',
            'fonts.googleapis.com',
            'fonts.gstatic.com',
        ];

        if (! in_array(strtolower($host), $allowedHosts, true)) {
            return false;
        }

        $allowedExtensions = [
            'css',
            'js',
            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif',
            'svg',
            'ico',
            'woff',
            'woff2',
            'ttf',
            'eot',
            'pdf',
        ];

        if ($extension === '') {
            return Str::contains($path, ['/wp-content/', '/wp-includes/', '/fonts/', '/css', '/js']);
        }

        return in_array($extension, $allowedExtensions, true);
    }

    private function downloadAsset(string $url): array
    {
        $relativePath = $this->buildRelativePath($url);
        $absolutePath = public_path($relativePath);

        File::ensureDirectoryExists(dirname($absolutePath));

        if (File::exists($absolutePath) && File::size($absolutePath) > 0) {
            return [
                'ok' => true,
                'local' => '/'.$relativePath,
                'downloaded' => false,
                'error' => null,
            ];
        }

        try {
            $response = Http::timeout(8)->withHeaders([
                'User-Agent' => 'Laravel Legacy Importer/1.0',
            ])->get($url);
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'local' => null,
                'downloaded' => false,
                'error' => $exception->getMessage(),
            ];
        }

        if (! $response->successful() || empty($response->body())) {
            return [
                'ok' => false,
                'local' => null,
                'downloaded' => false,
                'error' => 'HTTP '.$response->status(),
            ];
        }

        $absolutePath = $this->ensureExtensionByContentType($absolutePath, (string) $response->header('Content-Type'));
        $relativePath = ltrim(str_replace(public_path().DIRECTORY_SEPARATOR, '', $absolutePath), DIRECTORY_SEPARATOR);
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

        File::ensureDirectoryExists(dirname($absolutePath));
        File::put($absolutePath, $response->body());

        return [
            'ok' => true,
            'local' => '/'.$relativePath,
            'downloaded' => true,
            'error' => null,
        ];
    }

    private function buildRelativePath(string $url): string
    {
        $parts = parse_url($url);

        $host = strtolower($parts['host'] ?? 'external');
        $host = preg_replace('/[^a-z0-9.\-]/', '-', $host) ?: 'external';

        $path = $parts['path'] ?? '/index';

        if ($path === '' || Str::endsWith($path, '/')) {
            $path .= 'index';
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        $safeSegments = array_map(function (string $segment): string {
            $segment = rawurldecode($segment);
            $segment = preg_replace('/[^A-Za-z0-9._\-]+/', '-', $segment) ?: 'file';

            return trim($segment, '-');
        }, $segments);

        if (empty($safeSegments)) {
            $safeSegments = ['index'];
        }

        $basename = array_pop($safeSegments);
        $query = $parts['query'] ?? null;

        if ($query) {
            $extension = pathinfo($basename, PATHINFO_EXTENSION);
            $name = pathinfo($basename, PATHINFO_FILENAME);
            $suffix = '-'.substr(md5($query), 0, 10);

            $basename = $extension
                ? $name.$suffix.'.'.$extension
                : $name.$suffix;
        }

        $fullPath = 'legacy/assets/'.$host;

        if (! empty($safeSegments)) {
            $fullPath .= '/'.implode('/', $safeSegments);
        }

        return trim($fullPath.'/'.$basename, '/');
    }

    private function ensureExtensionByContentType(string $absolutePath, string $contentType): string
    {
        if (pathinfo($absolutePath, PATHINFO_EXTENSION) !== '') {
            return $absolutePath;
        }

        $normalized = strtolower(trim(explode(';', $contentType)[0] ?? ''));

        $map = [
            'text/css' => 'css',
            'application/javascript' => 'js',
            'text/javascript' => 'js',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            'font/woff2' => 'woff2',
            'font/woff' => 'woff',
            'font/ttf' => 'ttf',
            'application/pdf' => 'pdf',
        ];

        $extension = $map[$normalized] ?? null;

        if (! $extension) {
            return $absolutePath;
        }

        return $absolutePath.'.'.$extension;
    }

    private function rewriteHtml(string $html, array $urlMap): string
    {
        $rewritten = $html;

        foreach ($urlMap as $url => $local) {
            $rewritten = str_replace($url, $local, $rewritten);
        }

        $rewritten = preg_replace_callback(
            '/\/\*\s*savepage-url=([^*\s]+)\s*\*\/url\((?:[^)]*)\)/iu',
            static function (array $matches) use ($urlMap): string {
                $url = html_entity_decode(trim($matches[1]), ENT_QUOTES | ENT_HTML5);

                if (! isset($urlMap[$url])) {
                    return $matches[0];
                }

                return "url('{$urlMap[$url]}')";
            },
            $rewritten,
        ) ?? $rewritten;

        return $rewritten;
    }
}
