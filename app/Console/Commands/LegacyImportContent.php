<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LegacyImportContent extends Command
{
    protected $signature = 'legacy:import-content {--source=} {--with-sanitize : Run legacy:sanitize-html before import} {--with-assets : Run legacy:fetch-assets before import}';

    protected $description = 'Import sanitized legacy HTML into pages, products, categories, and blog posts';

    public function handle(): int
    {
        if ($this->option('with-sanitize')) {
            $sourceForSanitize = storage_path('app/legacy/source');
            $this->call('legacy:sanitize-html', ['--source' => $sourceForSanitize]);
        }

        if ($this->option('with-assets')) {
            $this->call('legacy:fetch-assets');
        }

        $sourceDir = $this->option('source') ?: storage_path('app/legacy/sanitized');

        if (! File::isDirectory($sourceDir)) {
            $this->error("Source directory not found: {$sourceDir}");

            return self::FAILURE;
        }

        $files = collect(File::files($sourceDir))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'html')
            ->values();

        if ($files->isEmpty()) {
            $this->warn("No HTML files found in {$sourceDir}");

            return self::SUCCESS;
        }

        $stats = [
            'pages_created' => 0,
            'pages_updated' => 0,
            'categories_created' => 0,
            'categories_updated' => 0,
            'products_created' => 0,
            'products_updated' => 0,
            'posts_created' => 0,
            'posts_updated' => 0,
        ];

        $seenCanonicalFiles = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $canonicalName = preg_replace('/\s\(\d+\)(?=\.html$)/u', '', $filename) ?? $filename;

            if (isset($seenCanonicalFiles[$canonicalName])) {
                continue;
            }
            $seenCanonicalFiles[$canonicalName] = true;

            $nameLower = $this->lower($canonicalName);
            $html = File::get($file->getPathname());
            $xpath = $this->createXPath($html);

            if (! $xpath) {
                $this->warn("Skip unreadable HTML: {$filename}");
                continue;
            }

            $this->importStaticPageIfMatched($nameLower, $xpath, $stats);
            $this->importProductDetailIfMatched($nameLower, $xpath, $stats);
            $this->importCatalogProductsIfMatched($nameLower, $xpath, $stats);
            $this->importPostsIfMatched($nameLower, $xpath, $stats);
        }

        foreach ($stats as $key => $value) {
            $this->line(str_pad($key, 22, ' ').": {$value}");
        }

        return self::SUCCESS;
    }

    private function importStaticPageIfMatched(string $nameLower, DOMXPath $xpath, array &$stats): void
    {
        $mapping = [
            'главная' => ['slug' => 'home', 'title' => 'Главная'],
            'контакты' => ['slug' => 'contacts', 'title' => 'Контакты'],
            'политика конфиденциальности' => ['slug' => 'policy', 'title' => 'Политика конфиденциальности'],
            'правила торговли' => ['slug' => 'pravila-torgovli', 'title' => 'Правила торговли'],
            'способ доставки' => ['slug' => 'sposob-dostavki', 'title' => 'Способ доставки'],
            'как сделать заказ' => ['slug' => 'kak-sdelat-zakaz', 'title' => 'Как сделать заказ'],
        ];

        foreach ($mapping as $needle => $target) {
            if (! str_contains($nameLower, $needle)) {
                continue;
            }

            $title = $this->extractTitle($xpath) ?: $target['title'];
            $content = $this->extractMainHtml($xpath);

            $page = Page::query()->updateOrCreate(
                ['slug' => $target['slug']],
                [
                    'title' => $title,
                    'content' => $content,
                    'is_published' => true,
                ]
            );

            if ($page->wasRecentlyCreated) {
                $stats['pages_created']++;
            } else {
                $stats['pages_updated']++;
            }

            return;
        }
    }

    private function importProductDetailIfMatched(string $nameLower, DOMXPath $xpath, array &$stats): void
    {
        $skipIfContains = ['главная', 'каталог', 'контакты', 'корзина', 'оформление заказа', 'наш блог', 'архивы', 'политика', 'правила торговли', 'способ доставки', 'как сделать заказ'];

        foreach ($skipIfContains as $needle) {
            if (str_contains($nameLower, $needle)) {
                return;
            }
        }

        $title = $this->firstNodeText(
            $xpath,
            "//h1[contains(@class,'product_title')] | //h1[contains(@class,'product-title')] | //h1"
        );

        if (! $title) {
            return;
        }

        $slug = $this->slugify($title);

        if (! $slug) {
            $slug = 'product-'.substr(md5($title), 0, 12);
        }

        $categoryName = $this->firstNodeText(
            $xpath,
            "//a[contains(@href,'product-category')][1]"
        ) ?: 'Без категории';

        $categorySlug = $this->slugify($categoryName) ?: 'bez-kategorii';

        $category = Category::query()->updateOrCreate(
            ['slug' => $categorySlug],
            [
                'name' => $categoryName,
                'is_active' => true,
            ],
        );

        if ($category->wasRecentlyCreated) {
            $stats['categories_created']++;
        } else {
            $stats['categories_updated']++;
        }

        $shortDescription = $this->collectNodeText(
            $xpath,
            "//div[contains(@class,'woocommerce-product-details__short-description')]"
        );

        $descriptionHtml = $this->extractFirstNodeHtml(
            $xpath,
            "//div[@id='tab-description'] | //div[contains(@class,'product__tabs__content')] | //div[contains(@class,'entry-content')]"
        );

        if (! $descriptionHtml) {
            $descriptionHtml = $shortDescription ? '<p>'.e($shortDescription).'</p>' : null;
        }

        $price = $this->extractPrice($xpath) ?? 0;

        $product = Product::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'category_id' => $category->id,
                'name' => $title,
                'sku' => 'SKU-'.strtoupper(substr(md5($slug), 0, 8)),
                'short_description' => $shortDescription,
                'description' => $descriptionHtml,
                'price' => $price,
                'old_price' => null,
                'stock' => 20,
                'is_active' => true,
            ]
        );

        if ($product->wasRecentlyCreated) {
            $stats['products_created']++;
        } else {
            $stats['products_updated']++;
        }

        $imagePaths = $this->extractProductImagePaths($xpath);

        if (! empty($imagePaths)) {
            $product->images()->delete();

            foreach (array_values($imagePaths) as $index => $path) {
                $product->images()->create([
                    'path' => $path,
                    'alt' => $title,
                    'sort_order' => $index,
                ]);
            }
        }
    }

    private function importCatalogProductsIfMatched(string $nameLower, DOMXPath $xpath, array &$stats): void
    {
        if (! str_contains($nameLower, 'каталог')) {
            return;
        }

        $category = Category::query()->firstOrCreate(
            ['slug' => 'catalog'],
            [
                'name' => 'Каталог',
                'is_active' => true,
            ]
        );

        if ($category->wasRecentlyCreated) {
            $stats['categories_created']++;
        }

        $nodes = $xpath->query("//li[contains(@class,'product')]//a[contains(@href, '/product/')]");

        if (! $nodes) {
            return;
        }

        foreach ($nodes as $node) {
            $name = trim($node->textContent);
            if (! $name) {
                continue;
            }

            $href = trim((string) $node->attributes?->getNamedItem('href')?->nodeValue);
            if (! $href) {
                continue;
            }

            $path = parse_url($href, PHP_URL_PATH);
            if (! $path) {
                continue;
            }

            $segments = array_values(array_filter(explode('/', $path)));
            $tail = end($segments);
            $slug = $this->slugify(($tail ? trim((string) $tail, '/') : '') ?: $name);

            if (! $slug) {
                continue;
            }

            $product = Product::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'sku' => 'SKU-'.strtoupper(substr(md5($slug), 0, 8)),
                    'price' => 0,
                    'stock' => 20,
                    'is_active' => true,
                ]
            );

            if ($product->wasRecentlyCreated) {
                $stats['products_created']++;
            }
        }
    }

    private function importPostsIfMatched(string $nameLower, DOMXPath $xpath, array &$stats): void
    {
        if (! str_contains($nameLower, 'блог') && ! str_contains($nameLower, 'архивы')) {
            return;
        }

        $articleNodes = $xpath->query("//article | //div[contains(@class,'blog-section__card')]");

        if ($articleNodes && $articleNodes->length > 0) {
            foreach ($articleNodes as $articleNode) {
                $title = $this->nodeTextByQuery(
                    $xpath,
                    ".//h2 | .//h3 | .//h4 | .//a[contains(@href, '/blog/')][1]",
                    $articleNode,
                );

                if (! $title) {
                    continue;
                }

                $href = $this->nodeAttributeByQuery(
                    $xpath,
                    ".//a[contains(@href, '/blog/')][1]",
                    'href',
                    $articleNode,
                );

                $slug = $href
                    ? $this->slugFromUrl($href)
                    : $this->slugify($title);

                if (! $slug) {
                    continue;
                }

                $excerpt = $this->nodeTextByQuery($xpath, './/p[1]', $articleNode);

                $post = Post::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $title,
                        'excerpt' => $excerpt,
                        'content' => $excerpt ? '<p>'.e($excerpt).'</p>' : null,
                        'published_at' => now(),
                        'is_published' => true,
                    ]
                );

                if ($post->wasRecentlyCreated) {
                    $stats['posts_created']++;
                } else {
                    $stats['posts_updated']++;
                }
            }

            return;
        }

        $fallbackTitle = $this->extractTitle($xpath);

        if (! $fallbackTitle) {
            return;
        }

        $slug = $this->slugify($fallbackTitle) ?: 'post-'.substr(md5($fallbackTitle), 0, 12);

        $post = Post::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $fallbackTitle,
                'excerpt' => null,
                'content' => $this->extractMainHtml($xpath),
                'published_at' => now(),
                'is_published' => true,
            ]
        );

        if ($post->wasRecentlyCreated) {
            $stats['posts_created']++;
        } else {
            $stats['posts_updated']++;
        }
    }

    private function createXPath(string $html): ?DOMXPath
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $loaded = $dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_COMPACT | LIBXML_NONET);

        libxml_clear_errors();

        if (! $loaded) {
            return null;
        }

        return new DOMXPath($dom);
    }

    private function extractTitle(DOMXPath $xpath): ?string
    {
        $title = $this->firstNodeText($xpath, '//title') ?: $this->firstNodeText($xpath, '//h1');

        if (! $title) {
            return null;
        }

        $title = preg_replace('/\s*[-–|].*$/u', '', $title) ?? $title;

        return trim($title);
    }

    private function extractMainHtml(DOMXPath $xpath): string
    {
        $queries = [
            "//main",
            "//div[contains(@class,'site-content')]",
            "//div[contains(@class,'content-area')]",
            "//article",
            "//body",
        ];

        $selectedNode = null;

        foreach ($queries as $query) {
            $node = $xpath->query($query)?->item(0);

            if (! $node) {
                continue;
            }

            if (trim($node->textContent) === '') {
                continue;
            }

            $selectedNode = $node;
            break;
        }

        if (! $selectedNode) {
            return '';
        }

        $html = $this->innerHtml($selectedNode);

        $html = preg_replace('/<(script|style)[^>]*>.*?<\/\1>/is', '', $html) ?? $html;
        $html = preg_replace('/<div[^>]*id=("|\')(wpadminbar|query-monitor-main|qmwrapper)\1[^>]*>.*?<\/div>/is', '', $html) ?? $html;

        return trim($html);
    }

    private function extractProductImagePaths(DOMXPath $xpath): array
    {
        $queries = [
            "//div[contains(@class,'woocommerce-product-gallery')]//img/@src",
            "//img[contains(@class,'wp-post-image')]/@src",
            "//img[contains(@class,'product')]/@src",
        ];

        $paths = [];

        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if (! $nodes) {
                continue;
            }

            foreach ($nodes as $node) {
                $path = $this->normalizeAssetPath($node->nodeValue);
                if (! $path) {
                    continue;
                }
                $paths[$path] = $path;
            }
        }

        return array_values($paths);
    }

    private function normalizeAssetPath(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        $raw = html_entity_decode(trim($raw), ENT_QUOTES | ENT_HTML5);

        if ($raw === '' || Str::startsWith($raw, 'data:')) {
            return null;
        }

        if (Str::startsWith($raw, ['http://', 'https://'])) {
            $parts = parse_url($raw);
            $path = $parts['path'] ?? null;

            if (! $path) {
                return null;
            }

            $path = ltrim($path, '/');

            if (Str::contains($path, 'legacy/assets/')) {
                $start = strpos($path, 'legacy/assets/');
                return substr($path, $start);
            }

            return null;
        }

        if (Str::startsWith($raw, '/')) {
            return ltrim($raw, '/');
        }

        return ltrim($raw, './');
    }

    private function extractPrice(DOMXPath $xpath): ?float
    {
        $priceTexts = [
            $this->firstNodeText($xpath, "//*[contains(@class,'woocommerce-Price-amount')]"),
            $this->firstNodeText($xpath, "//*[contains(@class,'price')]"),
        ];

        foreach ($priceTexts as $text) {
            if (! $text) {
                continue;
            }

            if (preg_match('/([0-9\s]+(?:[\.,][0-9]{1,2})?)/u', $text, $matches)) {
                $normalized = str_replace([' ', ','], ['', '.'], $matches[1]);
                return (float) $normalized;
            }
        }

        return null;
    }

    private function extractFirstNodeHtml(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)?->item(0);

        return $node ? trim($this->innerHtml($node)) : null;
    }

    private function collectNodeText(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)?->item(0);

        if (! $node) {
            return null;
        }

        return trim(preg_replace('/\s+/u', ' ', $node->textContent) ?? $node->textContent);
    }

    private function firstNodeText(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)?->item(0);

        if (! $node) {
            return null;
        }

        return trim(preg_replace('/\s+/u', ' ', $node->textContent) ?? $node->textContent);
    }

    private function nodeTextByQuery(DOMXPath $xpath, string $query, DOMNode $context): ?string
    {
        $node = $xpath->query($query, $context)?->item(0);

        if (! $node) {
            return null;
        }

        return trim(preg_replace('/\s+/u', ' ', $node->textContent) ?? $node->textContent);
    }

    private function nodeAttributeByQuery(DOMXPath $xpath, string $query, string $attribute, DOMNode $context): ?string
    {
        $node = $xpath->query($query, $context)?->item(0);

        if (! $node || ! $node->attributes?->getNamedItem($attribute)) {
            return null;
        }

        return trim((string) $node->attributes->getNamedItem($attribute)->nodeValue);
    }

    private function slugFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (! $path) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        if (empty($segments)) {
            return null;
        }

        return $this->slugify((string) end($segments));
    }

    private function slugify(string $value): string
    {
        return (string) Str::slug($value, '-', 'ru');
    }

    private function innerHtml(DOMNode $node): string
    {
        $html = '';

        foreach ($node->childNodes as $childNode) {
            $html .= $node->ownerDocument?->saveHTML($childNode);
        }

        return $html;
    }

    private function lower(string $value): string
    {
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($value, 'UTF-8');
        }

        return strtolower($value);
    }
}
