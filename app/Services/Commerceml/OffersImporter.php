<?php

namespace App\Services\Commerceml;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

class OffersImporter
{
    protected LoggerInterface $log;

    protected array $stats = [
        'products_updated' => 0,
        'products_created' => 0,
        'categories_created' => 0,
        'categories_updated' => 0,
    ];

    protected int $variantsCreated = 0;
    protected int $variantsUpdated = 0;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Импорт offers.xml — цены и остатки.
     */
    public function import(string $filePath): array
    {
        $xml = simplexml_load_file($filePath);

        if ($xml === false) {
            throw new \RuntimeException("Failed to parse XML: {$filePath}");
        }

        // Получить маппинг типов цен
        $priceTypes = $this->parsePriceTypes($xml);

        if (isset($xml->ПакетПредложений->Предложения->Предложение)) {
            foreach ($xml->ПакетПредложений->Предложения->Предложение as $offer) {
                $this->processOffer($offer, $priceTypes);
            }
        }

        $this->log->info('OffersImporter stats', [
            'products_updated' => $this->stats['products_updated'],
            'variants_created' => $this->variantsCreated,
            'variants_updated' => $this->variantsUpdated,
        ]);

        return $this->stats;
    }

    /**
     * Парсинг типов цен из <ТипыЦен>.
     * Возвращает массив [GUID => название].
     */
    protected function parsePriceTypes(\SimpleXMLElement $xml): array
    {
        $types = [];

        if (isset($xml->ПакетПредложений->ТипыЦен->ТипЦены)) {
            foreach ($xml->ПакетПредложений->ТипыЦен->ТипЦены as $type) {
                $types[(string) $type->Ид] = (string) $type->Наименование;
            }
        }

        return $types;
    }

    /**
     * Обработка одного предложения (товар или вариант).
     */
    protected function processOffer(\SimpleXMLElement $offer, array $priceTypes): void
    {
        $guid = (string) $offer->Ид;
        $sku = (string) ($offer->Артикул ?? '');
        $stock = (int) ($offer->Количество ?? 0);

        // Парсинг цен
        $prices = $this->parsePrices($offer, $priceTypes);

        // Определить: простой товар или вариант
        if (str_contains($guid, '#')) {
            // Вариант: product-guid#variant-guid
            [$productGuid, $variantGuid] = explode('#', $guid, 2);
            $this->processVariantOffer($productGuid, $variantGuid, $sku, $stock, $prices, $offer);
        } else {
            // Простой товар
            $this->processSimpleOffer($guid, $sku, $stock, $prices);
        }
    }

    /**
     * Обновить цену и остаток простого товара.
     */
    protected function processSimpleOffer(string $guid, string $sku, int $stock, array $prices): void
    {
        $product = Product::where('external_id', $guid)->first();

        if (! $product && ! empty($sku)) {
            $product = Product::where('sku', $sku)->first();
        }

        if (! $product) {
            $this->log->warning("OffersImporter: product not found", ['guid' => $guid, 'sku' => $sku]);
            return;
        }

        $updateData = ['stock' => $stock];

        if (! empty($product->external_id) || $product->external_id !== $guid) {
            $updateData['external_id'] = $guid;
        }

        if (isset($prices['price'])) {
            $updateData['price'] = $prices['price'];
        }
        if (isset($prices['old_price'])) {
            $updateData['old_price'] = $prices['old_price'];
        }

        $product->update($updateData);
        $this->stats['products_updated']++;

        $this->log->debug("Product price/stock updated: {$product->name}", [
            'id' => $product->id,
            'price' => $prices['price'] ?? null,
            'stock' => $stock,
        ]);
    }

    /**
     * Обработка варианта товара (предложение с характеристиками).
     */
    protected function processVariantOffer(
        string $productGuid,
        string $variantGuid,
        string $sku,
        int $stock,
        array $prices,
        \SimpleXMLElement $offer
    ): void {
        $product = Product::where('external_id', $productGuid)->first();

        if (! $product) {
            $this->log->warning("OffersImporter: parent product not found", ['guid' => $productGuid]);
            return;
        }

        // Найти или создать вариант
        $variant = ProductVariant::where('external_id', $variantGuid)->first();

        $variantData = [
            'product_id' => $product->id,
            'external_id' => $variantGuid,
            'stock' => $stock,
            'is_active' => true,
        ];

        if (! empty($sku)) {
            $variantData['sku'] = $sku;
        }
        if (isset($prices['price'])) {
            $variantData['price'] = $prices['price'];
        }
        if (isset($prices['old_price'])) {
            $variantData['old_price'] = $prices['old_price'];
        }

        if ($variant) {
            $variant->update($variantData);
            $this->variantsUpdated++;
        } else {
            $variantData['sort_order'] = ProductVariant::where('product_id', $product->id)->count();
            $variant = ProductVariant::create($variantData);
            $this->variantsCreated++;
        }

        // Привязать характеристики (размер, цвет и т.д.)
        $this->syncVariantAttributes($variant, $offer);
    }

    /**
     * Привязать характеристики варианта из <ХарактеристикиТовара>.
     */
    protected function syncVariantAttributes(ProductVariant $variant, \SimpleXMLElement $offer): void
    {
        if (! isset($offer->ХарактеристикиТовара->ХарактеристикаТовара)) {
            return;
        }

        $pivotData = [];

        foreach ($offer->ХарактеристикиТовара->ХарактеристикаТовара as $char) {
            $attrName = (string) $char->Наименование;
            $attrValue = (string) $char->Значение;

            if (empty($attrName) || empty($attrValue)) {
                continue;
            }

            // Найти или создать атрибут (Размер, Цвет и т.д.)
            $attribute = ProductAttribute::firstOrCreate(
                ['slug' => Str::slug($attrName)],
                ['name' => $attrName, 'sort_order' => 0]
            );

            // Найти или создать значение атрибута
            $value = ProductAttributeValue::firstOrCreate(
                [
                    'product_attribute_id' => $attribute->id,
                    'slug' => Str::slug($attrValue),
                ],
                [
                    'value' => $attrValue,
                    'sort_order' => 0,
                ]
            );

            $pivotData[$value->id] = ['product_attribute_id' => $attribute->id];
        }

        if (! empty($pivotData)) {
            $variant->attributeValues()->sync($pivotData);
        }
    }

    /**
     * Парсинг цен предложения.
     * Маппинг типа цены из конфига → price / old_price.
     */
    protected function parsePrices(\SimpleXMLElement $offer, array $priceTypes): array
    {
        $result = [];
        $mainPriceType = config('commerceml.price_type', 'Розничная');
        $oldPriceType = config('commerceml.old_price_type', '');

        if (! isset($offer->Цены->Цена)) {
            return $result;
        }

        foreach ($offer->Цены->Цена as $price) {
            $priceValue = (float) $price->ЦенаЗаЕдиницу;
            $typeGuid = (string) ($price->ИдТипаЦены ?? $price->ТипЦены->Ид ?? '');
            $typeName = $priceTypes[$typeGuid] ?? (string) ($price->ТипЦены->Наименование ?? '');

            if ($typeName === $mainPriceType || (empty($typeName) && ! isset($result['price']))) {
                $result['price'] = $priceValue;
            } elseif (! empty($oldPriceType) && $typeName === $oldPriceType) {
                $result['old_price'] = $priceValue;
            }
        }

        // Если только одна цена без типа, считать её основной
        if (empty($result) && isset($offer->Цены->Цена)) {
            $result['price'] = (float) $offer->Цены->Цена->ЦенаЗаЕдиницу;
        }

        return $result;
    }
}
