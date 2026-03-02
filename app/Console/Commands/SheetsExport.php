<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;

class SheetsExport extends Command
{
    protected $signature = 'sheets:export {--active-only : Только активные товары}';

    protected $description = 'Экспорт товаров в Google Таблицу';

    public function handle(): int
    {
        $this->info('Загрузка товаров...');

        $query = Product::query()
            ->with([
                'category.parent.parent',
                'variants.attributeValues.attribute',
                'images',
                'specifications',
            ]);

        if ($this->option('active-only')) {
            $query->where('is_active', true);
        }

        $products = $query->get();

        $header = [
            'Артикул Мой Склад',     // A
            'Раздел',                  // B
            'Подраздел',               // C
            'Подраздел подраздела',    // D
            '',                        // E
            'Последовательность в каталоге', // F
            'Последовательность в разделе',  // G
            'Материнское имя',         // H
            'Аббревиатура',            // I
            'Наименование',            // J
            'Цена',                    // K
            'Старая',                  // L
            'Масса (XXX)',             // M
            'Площадь балл. защиты',    // N
            'Статус товара',           // O
            'Ссылка на фотографии',    // P
            'Основные характеристики', // Q
            'Рекомендованные товары',   // R
            'Информация',              // S
            'Преимущества',            // T
            'Рекомендации по эксплуатации', // U
            'Гарантия и ремонт',       // V
            'Сертификаты и протоколы',  // W
            'Статус на сайте (0 - не выкладывать, 1 - выкладывать)', // X
        ];

        $rows = [];

        foreach ($products as $product) {
            $categoryChain = $this->getCategoryChain($product);

            if ($product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    $rows[] = $this->buildRow($product, $categoryChain, $variant);
                }
            } else {
                $rows[] = $this->buildRow($product, $categoryChain);
            }
        }

        $this->info("Подготовлено строк: " . count($rows));
        $this->info('Отправка в Google Таблицу...');

        $service = app(GoogleSheetsService::class);
        $written = $service->writeAllRows($header, $rows);

        $this->info("Экспортировано {$written} строк в Google Таблицу.");

        return self::SUCCESS;
    }

    private function buildRow(Product $product, array $categoryChain, $variant = null): array
    {
        $sku = $variant?->sku ?: $product->sku;
        $price = $variant ? $variant->price : $product->price;
        $oldPrice = $variant ? $variant->old_price : $product->old_price;
        $isActive = $variant ? $variant->is_active : $product->is_active;

        // Build full variant name
        $fullName = $product->name;
        if ($variant) {
            $attrParts = [];
            foreach ($variant->attributeValues as $attrVal) {
                $attrParts[] = $attrVal->value;
            }
            if ($attrParts) {
                $fullName .= ' — ' . implode(', ', $attrParts);
            }
        }

        // Specifications
        $specs = $product->specifications->keyBy('name');
        $mass = $specs->get('Масса')?->value ?? $specs->get('Вес')?->value ?? '';
        $area = $specs->get('Площадь балл. защиты')?->value
            ?? $specs->get('Площадь баллистической защиты')?->value ?? '';

        // Images — all URLs separated by newline
        $imageUrls = $product->images->pluck('path')->filter()->implode("\n");

        return [
            $sku ?? '',                                      // A — Артикул
            $categoryChain[0] ?? '',                         // B — Раздел
            $categoryChain[1] ?? '',                         // C — Подраздел
            $categoryChain[2] ?? '',                         // D — Подраздел подраздела
            '',                                              // E
            '',                                              // F — Последовательность в каталоге
            '',                                              // G — Последовательность в разделе
            $product->name,                                  // H — Материнское имя
            $product->short_description ?? '',               // I — Аббревиатура
            $fullName,                                       // J — Наименование
            $price !== null ? (string) $price : '',          // K — Цена
            $oldPrice !== null ? (string) $oldPrice : '',    // L — Старая
            $mass,                                           // M — Масса
            $area,                                           // N — Площадь балл. защиты
            $isActive ? 'Основной' : 'Снят с продажи',       // O — Статус
            $imageUrls,                                      // P — Фотографии
            $product->description ?? '',                     // Q — Основные характеристики
            '',                                              // R — Рекомендованные товары
            '',                                              // S — Информация
            '',                                              // T — Преимущества
            '',                                              // U — Рекомендации
            '',                                              // V — Гарантия
            '',                                              // W — Сертификаты
            $isActive ? '1' : '0',                           // X — Статус на сайте
        ];
    }

    private function getCategoryChain(Product $product): array
    {
        $chain = [];
        $category = $product->category;

        while ($category) {
            array_unshift($chain, $category->name);
            $category = $category->parent;
        }

        // Pad to at least 3 levels
        return array_pad($chain, 3, '');
    }
}
