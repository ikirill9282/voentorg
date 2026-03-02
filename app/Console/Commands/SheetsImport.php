<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SheetsImport extends Command
{
    protected $signature = 'sheets:import {--dry-run : Показать изменения без записи в БД}';

    protected $description = 'Импорт товаров из Google Таблицы';

    // Column indices (0-based)
    private const COL_SKU = 0;         // A — Артикул Мой Склад
    private const COL_SECTION = 1;     // B — Раздел
    private const COL_SUBSECTION = 2;  // C — Подраздел
    private const COL_SUBSUB = 3;      // D — Подраздел подраздела
    private const COL_PARENT_NAME = 7; // H — Материнское имя
    private const COL_ABBREV = 8;      // I — Аббревиатура
    private const COL_FULL_NAME = 9;   // J — Наименование
    private const COL_PRICE = 10;      // K — Цена
    private const COL_OLD_PRICE = 11;  // L — Старая цена
    private const COL_MASS = 12;       // M — Масса
    private const COL_AREA = 13;       // N — Площадь балл. защиты
    private const COL_STATUS = 14;     // O — Статус товара
    private const COL_PHOTOS = 15;     // P — Ссылка на фотографии
    private const COL_DESC = 16;       // Q — Основные характеристики
    private const COL_INFO = 18;       // S — Информация
    private const COL_ADVANTAGES = 19; // T — Преимущества
    private const COL_SITE_STATUS = 23; // X — Статус на сайте (0/1)

    private bool $dryRun = false;

    private int $created = 0;

    private int $updated = 0;

    private int $variantsCreated = 0;

    private int $variantsUpdated = 0;

    public function handle(): int
    {
        $this->dryRun = (bool) $this->option('dry-run');

        if ($this->dryRun) {
            $this->warn('=== DRY RUN — изменения НЕ будут сохранены ===');
        }

        $this->info('Чтение данных из Google Таблицы...');

        $service = app(GoogleSheetsService::class);
        $rows = $service->readAllRows();

        if (empty($rows)) {
            $this->warn('Таблица пуста.');

            return self::SUCCESS;
        }

        $this->info('Получено строк: ' . count($rows));

        // Split rows into groups by empty-row separators
        $groups = $this->splitIntoGroups($rows);

        $this->info('Найдено товаров (групп): ' . count($groups));

        foreach ($groups as $groupRows) {
            $this->processProductGroup($groupRows);
        }

        $this->newLine();
        $this->info("--- Результат ---");
        $this->info("Товаров создано: {$this->created}");
        $this->info("Товаров обновлено: {$this->updated}");
        $this->info("Вариантов создано: {$this->variantsCreated}");
        $this->info("Вариантов обновлено: {$this->variantsUpdated}");

        return self::SUCCESS;
    }

    /**
     * Split sheet rows into product groups separated by empty rows.
     * Each group is a collection of data rows for one product.
     */
    private function splitIntoGroups(array $rows): array
    {
        $groups = [];
        $current = [];

        foreach ($rows as $row) {
            $sku = trim($this->col($row, self::COL_SKU));
            $fullName = trim($this->col($row, self::COL_FULL_NAME));

            // Empty row = separator between product groups
            if ($sku === '' && $fullName === '') {
                if (! empty($current)) {
                    $groups[] = collect($current);
                    $current = [];
                }

                continue;
            }

            $current[] = $row;
        }

        // Don't forget the last group
        if (! empty($current)) {
            $groups[] = collect($current);
        }

        return $groups;
    }

    private function processProductGroup(Collection $rows): void
    {
        $firstRow = $rows->first();

        // Determine product name: column H if filled, otherwise extract from J
        $parentName = trim($this->col($firstRow, self::COL_PARENT_NAME));

        if (! $parentName) {
            $parentName = $this->extractProductName($this->col($firstRow, self::COL_FULL_NAME));
        }

        if (! $parentName) {
            return; // Can't determine product name
        }

        $sku = trim($this->col($firstRow, self::COL_SKU));

        // Find existing product by name or SKU
        $product = Product::query()
            ->where('name', $parentName)
            ->orWhere(function ($q) use ($sku) {
                if ($sku) {
                    $q->where('sku', $sku);
                }
            })
            ->first();

        // Resolve category
        $categoryId = $this->resolveCategory($firstRow);

        // Determine main product attributes from first row
        $price = $this->parseDecimal($this->col($firstRow, self::COL_PRICE));
        $oldPrice = $this->parseDecimal($this->col($firstRow, self::COL_OLD_PRICE));
        $isActive = $this->parseIsActive($firstRow);
        $shortDesc = trim($this->col($firstRow, self::COL_ABBREV));
        $description = trim($this->col($firstRow, self::COL_DESC));

        $productData = [
            'name' => $parentName,
            'slug' => Str::slug($parentName),
            'sku' => $rows->count() === 1 ? $sku : ($product->sku ?? $sku),
            'category_id' => $categoryId,
            'price' => $price,
            'old_price' => $oldPrice,
            'is_active' => $isActive,
            'short_description' => $shortDesc ?: null,
            'description' => $description ?: null,
        ];

        $isNew = ! $product;

        if ($this->dryRun) {
            if ($isNew) {
                $this->line("  [CREATE] Товар: {$parentName} ({$rows->count()} вариантов)");
                $this->created++;
            } else {
                $changes = $this->detectChanges($product, $productData);
                if ($changes) {
                    $this->line("  [UPDATE] Товар: {$parentName} — {$changes}");
                    $this->updated++;
                }
            }
        } else {
            if ($isNew) {
                $product = Product::create($productData);
                $this->created++;
                $this->line("  [CREATE] Товар: {$parentName}");
            } else {
                $product->update($productData);
                $this->updated++;
            }
        }

        // Process variants if more than 1 row
        if ($rows->count() > 1) {
            foreach ($rows as $row) {
                $this->processVariant($product, $row);
            }
        }

        // Update images from first row
        $this->processImages($product, $firstRow);

        // Update specifications
        $this->processSpecifications($product, $firstRow);
    }

    /**
     * Extract base product name from the full variant name (column J).
     * Pattern: "Муромец М5, Размер M, цв. Хаки" → "Муромец М5"
     */
    private function extractProductName(string $fullName): string
    {
        $fullName = trim($fullName);

        if ($fullName === '') {
            return '';
        }

        // Split by comma and take the first part
        $parts = explode(',', $fullName);
        $name = trim($parts[0]);

        // Remove trailing size/color/protection markers
        $name = preg_replace('/\s+(Размер|бп\.|цв\.|Бр\d).*$/u', '', $name);

        return trim($name);
    }

    private function processVariant(?Product $product, array $row): void
    {
        $sku = trim($this->col($row, self::COL_SKU));
        $price = $this->parseDecimal($this->col($row, self::COL_PRICE));
        $oldPrice = $this->parseDecimal($this->col($row, self::COL_OLD_PRICE));
        $isActive = $this->parseIsActive($row);
        $fullName = trim($this->col($row, self::COL_FULL_NAME));

        if (! $sku && ! $fullName) {
            return;
        }

        $variantData = [
            'sku' => $sku ?: null,
            'price' => $price,
            'old_price' => $oldPrice,
            'is_active' => $isActive,
        ];

        if ($this->dryRun) {
            if ($product) {
                $existing = $sku
                    ? $product->variants()->where('sku', $sku)->first()
                    : null;

                if ($existing) {
                    $this->variantsUpdated++;
                } else {
                    $this->variantsCreated++;
                }
            } else {
                $this->variantsCreated++;
            }

            return;
        }

        if (! $product) {
            return;
        }

        $existing = $sku
            ? $product->variants()->where('sku', $sku)->first()
            : null;

        if ($existing) {
            $existing->update($variantData);
            $this->variantsUpdated++;
        } else {
            $product->variants()->create(array_merge($variantData, [
                'stock' => 0,
                'sort_order' => 0,
            ]));
            $this->variantsCreated++;
        }
    }

    private function processImages(?Product $product, array $row): void
    {
        $photosRaw = trim($this->col($row, self::COL_PHOTOS));

        if (! $photosRaw || $this->dryRun || ! $product) {
            return;
        }

        // Photos can be separated by newline, comma, or semicolon
        $urls = preg_split('/[\n,;]+/', $photosRaw);
        $urls = array_map('trim', $urls);
        $urls = array_filter($urls);

        if (empty($urls)) {
            return;
        }

        // Only update if images changed
        $existingPaths = $product->images()->pluck('path')->all();

        if ($urls === $existingPaths) {
            return;
        }

        $product->images()->delete();

        foreach ($urls as $i => $url) {
            $product->images()->create([
                'path' => $url,
                'alt' => $product->name,
                'sort_order' => $i,
            ]);
        }
    }

    private function processSpecifications(?Product $product, array $row): void
    {
        if ($this->dryRun || ! $product) {
            return;
        }

        $specs = [];

        $mass = trim($this->col($row, self::COL_MASS));
        if ($mass) {
            $specs['Масса'] = $mass;
        }

        $area = trim($this->col($row, self::COL_AREA));
        if ($area) {
            $specs['Площадь балл. защиты'] = $area;
        }

        foreach ($specs as $name => $value) {
            ProductSpecification::updateOrCreate(
                ['product_id' => $product->id, 'name' => $name],
                ['value' => $value, 'sort_order' => 0]
            );
        }
    }

    private function resolveCategory(array $row): ?int
    {
        $section = trim($this->col($row, self::COL_SECTION));
        $subsection = trim($this->col($row, self::COL_SUBSECTION));
        $subSub = trim($this->col($row, self::COL_SUBSUB));

        if (! $section) {
            return null;
        }

        if ($this->dryRun) {
            $cat = Category::where('name', $section)->whereNull('parent_id')->first();

            if ($subsection && $cat) {
                $sub = Category::where('name', $subsection)->where('parent_id', $cat->id)->first();
                if ($subSub && $sub) {
                    $subSubCat = Category::where('name', $subSub)->where('parent_id', $sub->id)->first();

                    return $subSubCat?->id ?? $sub->id;
                }

                return $sub?->id ?? $cat->id;
            }

            return $cat?->id;
        }

        // Create/find category hierarchy
        $parent = Category::firstOrCreate(
            ['name' => $section, 'parent_id' => null],
            ['slug' => Str::slug($section), 'is_active' => true, 'sort_order' => 0]
        );

        if (! $subsection) {
            return $parent->id;
        }

        $sub = Category::firstOrCreate(
            ['name' => $subsection, 'parent_id' => $parent->id],
            ['slug' => Str::slug($subsection), 'is_active' => true, 'sort_order' => 0]
        );

        if (! $subSub) {
            return $sub->id;
        }

        $subSubCat = Category::firstOrCreate(
            ['name' => $subSub, 'parent_id' => $sub->id],
            ['slug' => Str::slug($subSub), 'is_active' => true, 'sort_order' => 0]
        );

        return $subSubCat->id;
    }

    private function col(array $row, int $index): string
    {
        return (string) ($row[$index] ?? '');
    }

    private function parseDecimal(string $value): ?float
    {
        $value = str_replace([' ', ','], ['', '.'], trim($value));

        if ($value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function parseIsActive(array $row): bool
    {
        // Column X "Статус на сайте" takes priority: 1 = active, 0/empty = inactive
        $siteStatus = trim($this->col($row, self::COL_SITE_STATUS));
        if ($siteStatus !== '') {
            return $siteStatus === '1';
        }

        // Fallback to column O
        $status = mb_strtolower(trim($this->col($row, self::COL_STATUS)));
        if (in_array($status, ['снят с продажи', 'неактивен', 'нет', 'false', 'inactive', 'off', 'архив'])) {
            return false;
        }

        return true;
    }

    private function detectChanges(Product $product, array $data): string
    {
        $changes = [];

        foreach ($data as $key => $value) {
            $current = $product->{$key};

            if ($key === 'price' || $key === 'old_price') {
                if ((float) $current !== (float) ($value ?? 0)) {
                    $changes[] = "{$key}: {$current} → {$value}";
                }

                continue;
            }

            if ((string) $current !== (string) ($value ?? '')) {
                $changes[] = $key;
            }
        }

        return implode(', ', $changes);
    }
}
