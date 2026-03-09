<?php

namespace App\Services\Commerceml;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class CatalogImporter
{
    protected LoggerInterface $log;

    protected array $stats = [
        'categories_created' => 0,
        'categories_updated' => 0,
        'products_created' => 0,
        'products_updated' => 0,
    ];

    /** @var array<string, int> Кэш GUID → category_id */
    protected array $categoryMap = [];

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Импорт import.xml — категории + товары.
     */
    public function import(string $filePath, string $sessionDir): array
    {
        $xml = simplexml_load_file($filePath);

        if ($xml === false) {
            throw new \RuntimeException("Failed to parse XML: {$filePath}");
        }

        // 1. Импорт категорий (Классификатор → Группы)
        if (isset($xml->Классификатор->Группы)) {
            $this->importCategories($xml->Классификатор->Группы->Группа);
        }

        // 2. Импорт свойств (Классификатор → Свойства) — сохраняем маппинг для товаров
        $propertiesMap = [];
        if (isset($xml->Классификатор->Свойства)) {
            foreach ($xml->Классификатор->Свойства->Свойство as $prop) {
                $propId = (string) $prop->Ид;
                $propertiesMap[$propId] = (string) $prop->Наименование;
            }
        }

        // 3. Импорт товаров (Каталог → Товары)
        if (isset($xml->Каталог->Товары)) {
            $this->importProducts($xml->Каталог->Товары->Товар, $propertiesMap, $sessionDir);
        }

        $this->log->info('CatalogImporter stats', $this->stats);

        return $this->stats;
    }

    /**
     * Рекурсивный импорт категорий из <Группы>.
     */
    protected function importCategories(\SimpleXMLElement $groups, ?int $parentId = null): void
    {
        foreach ($groups as $group) {
            $guid = (string) $group->Ид;
            $name = (string) $group->Наименование;

            $category = $this->findCategory($guid, $name);

            if ($category) {
                $category->update([
                    'external_id' => $guid,
                    'name' => $name,
                    'parent_id' => $parentId,
                ]);
                $this->stats['categories_updated']++;
                $this->log->debug("Category updated: {$name}", ['id' => $category->id, 'guid' => $guid]);
            } else {
                $category = Category::create([
                    'external_id' => $guid,
                    'name' => $name,
                    'slug' => Str::slug($name) ?: Str::random(8),
                    'parent_id' => $parentId,
                    'is_active' => true,
                    'sort_order' => 0,
                ]);
                $this->stats['categories_created']++;
                $this->log->debug("Category created: {$name}", ['id' => $category->id, 'guid' => $guid]);
            }

            $this->categoryMap[$guid] = $category->id;

            // Рекурсия для вложенных групп
            if (isset($group->Группы->Группа)) {
                $this->importCategories($group->Группы->Группа, $category->id);
            }
        }
    }

    /**
     * Импорт товаров из <Товары>.
     */
    protected function importProducts(\SimpleXMLElement $products, array $propertiesMap, string $sessionDir): void
    {
        foreach ($products as $item) {
            $guid = (string) $item->Ид;
            // GUID может содержать #, берём только основной ID товара
            $productGuid = str_contains($guid, '#') ? explode('#', $guid)[0] : $guid;

            $sku = (string) ($item->Артикул ?? '');
            $name = (string) $item->Наименование;
            $description = (string) ($item->Описание ?? '');

            // Определить категорию
            $categoryId = null;
            if (isset($item->Группы->Ид)) {
                $catGuid = (string) $item->Группы->Ид;
                $categoryId = $this->categoryMap[$catGuid] ?? Category::where('external_id', $catGuid)->value('id');
            }

            $product = $this->findProduct($productGuid, $sku, $name);

            if ($product) {
                $updateData = [
                    'external_id' => $productGuid,
                    'name' => $name,
                ];

                if (! empty($sku)) {
                    $updateData['sku'] = $sku;
                }
                if (! empty($description)) {
                    $updateData['description'] = $description;
                }
                if ($categoryId) {
                    $updateData['category_id'] = $categoryId;
                }

                $product->update($updateData);
                $this->stats['products_updated']++;
                $this->log->debug("Product updated: {$name}", ['id' => $product->id, 'guid' => $productGuid]);
            } else {
                $product = Product::create([
                    'external_id' => $productGuid,
                    'name' => $name,
                    'slug' => Str::slug($name) ?: Str::random(8),
                    'sku' => $sku ?: null,
                    'description' => $description,
                    'category_id' => $categoryId,
                    'price' => 0,
                    'stock' => 0,
                    'is_active' => true,
                ]);
                $this->stats['products_created']++;
                $this->log->debug("Product created: {$name}", ['id' => $product->id, 'guid' => $productGuid]);
            }

            // Обработка изображений
            $this->importProductImages($product, $item, $sessionDir);

            // Обработка свойств → спецификации
            $this->importProductSpecifications($product, $item, $propertiesMap);
        }
    }

    /**
     * Импорт изображений товара.
     */
    protected function importProductImages(Product $product, \SimpleXMLElement $item, string $sessionDir): void
    {
        if (! isset($item->Картинка)) {
            return;
        }

        $storagePath = config('commerceml.image_storage', 'products');
        $publicPath = storage_path('app/public/' . $storagePath);

        if (! is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        $sortOrder = 0;
        foreach ($item->Картинка as $image) {
            $imagePath = (string) $image;
            if (empty($imagePath)) {
                continue;
            }

            $sourcePath = $sessionDir . '/' . $imagePath;
            if (! file_exists($sourcePath)) {
                $this->log->warning("Image not found: {$sourcePath}");
                continue;
            }

            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $newFilename = Str::random(32) . '.' . $ext;
            $destPath = $publicPath . '/' . $newFilename;

            copy($sourcePath, $destPath);

            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'path' => $storagePath . '/' . $newFilename,
                ],
                [
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }

    /**
     * Импорт свойств товара → ProductSpecification.
     */
    protected function importProductSpecifications(Product $product, \SimpleXMLElement $item, array $propertiesMap): void
    {
        if (! isset($item->ЗначенияСвойств->ЗначениеСвойств)) {
            return;
        }

        $sortOrder = 0;
        foreach ($item->ЗначенияСвойств->ЗначениеСвойств as $propValue) {
            $propId = (string) $propValue->Ид;
            $value = (string) $propValue->Значение;

            if (empty($value)) {
                continue;
            }

            $propName = $propertiesMap[$propId] ?? $propId;

            ProductSpecification::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $propName,
                ],
                [
                    'value' => $value,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }

    /**
     * Поиск существующей категории: по external_id → по name.
     */
    protected function findCategory(string $guid, string $name): ?Category
    {
        return Category::where('external_id', $guid)->first()
            ?? Category::where('name', $name)->whereNull('external_id')->first();
    }

    /**
     * Поиск существующего товара: по external_id → по SKU → по name.
     */
    protected function findProduct(string $guid, string $sku, string $name): ?Product
    {
        $product = Product::where('external_id', $guid)->first();
        if ($product) {
            return $product;
        }

        if (! empty($sku)) {
            $product = Product::where('sku', $sku)->whereNull('external_id')->first();
            if ($product) {
                return $product;
            }
        }

        return Product::where('name', $name)->whereNull('external_id')->first();
    }
}
