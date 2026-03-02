<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\DeliveryCompany;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WpImport extends Command
{
    protected $signature = 'wp:import
        {--fresh : Wipe existing data before importing}
        {--images : Download product images from old site}
        {--step= : Run only a specific step (categories,attributes,products,variations,images,blog,pages,orders)}';

    protected $description = 'Import data from old WordPress/WooCommerce database into Laravel';

    private string $conn = 'wordpress';

    private array $categoryMap = [];
    private array $attributeMap = [];
    private array $attributeValueMap = [];
    private array $productMap = [];

    public function handle(): int
    {
        $this->info('Starting WordPress data import...');

        $step = $this->option('step');

        if ($this->option('fresh')) {
            $this->wipeTables();
        }

        $steps = $step
            ? [$step]
            : ['categories', 'attributes', 'products', 'variations', 'images', 'blog', 'pages', 'orders'];

        foreach ($steps as $s) {
            match ($s) {
                'categories' => $this->importCategories(),
                'attributes' => $this->importAttributes(),
                'products' => $this->importProducts(),
                'variations' => $this->importVariations(),
                'images' => $this->importImages(),
                'blog' => $this->importBlogPosts(),
                'pages' => $this->importPages(),
                'orders' => $this->importOrders(),
                default => $this->error("Unknown step: {$s}"),
            };
        }

        $this->newLine();
        $this->info('Import completed!');

        return self::SUCCESS;
    }

    private function wipeTables(): void
    {
        $this->warn('Wiping existing data...');

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } else {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        OrderItem::truncate();
        Order::truncate();
        ProductSpecification::truncate();
        DB::table('product_variant_attributes')->truncate();
        ProductVariant::truncate();
        ProductImage::truncate();
        DB::table('category_product')->truncate();
        Product::truncate();
        ProductAttributeValue::truncate();
        ProductAttribute::truncate();
        Category::truncate();
        Post::truncate();
        Page::truncate();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } else {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    private function importCategories(): void
    {
        $this->info('Importing categories...');

        $wpCategories = DB::connection($this->conn)
            ->table('term_taxonomy as tt')
            ->join('terms as t', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'product_cat')
            ->select('t.term_id', 't.name', 't.slug', 'tt.description', 'tt.parent', 'tt.count')
            ->orderBy('tt.parent')
            ->orderBy('t.name')
            ->get();

        $parentPass = $wpCategories->where('parent', 0);
        $childPass = $wpCategories->where('parent', '!=', 0);

        $sortOrder = 0;
        foreach ($parentPass as $wpCat) {
            $category = Category::updateOrCreate(
                ['slug' => $wpCat->slug],
                [
                    'name' => $wpCat->name,
                    'description' => $wpCat->description ?: null,
                    'parent_id' => null,
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ],
            );
            $this->categoryMap[$wpCat->term_id] = $category->id;
        }

        foreach ($childPass as $wpCat) {
            $parentId = $this->categoryMap[$wpCat->parent] ?? null;
            $category = Category::updateOrCreate(
                ['slug' => $wpCat->slug],
                [
                    'name' => $wpCat->name,
                    'description' => $wpCat->description ?: null,
                    'parent_id' => $parentId,
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ],
            );
            $this->categoryMap[$wpCat->term_id] = $category->id;
        }

        $this->info("  Imported {$wpCategories->count()} categories.");
    }

    private function importAttributes(): void
    {
        $this->info('Importing product attributes...');

        $wpAttributes = DB::connection($this->conn)
            ->table('woocommerce_attribute_taxonomies')
            ->get();

        foreach ($wpAttributes as $wpAttr) {
            $attribute = ProductAttribute::updateOrCreate(
                ['slug' => $wpAttr->attribute_name],
                [
                    'name' => $wpAttr->attribute_label ?: $wpAttr->attribute_name,
                    'sort_order' => $wpAttr->attribute_id,
                ],
            );
            $this->attributeMap[$wpAttr->attribute_name] = $attribute->id;

            $taxonomy = 'pa_' . $wpAttr->attribute_name;

            $values = DB::connection($this->conn)
                ->table('term_taxonomy as tt')
                ->join('terms as t', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', $taxonomy)
                ->select('t.term_id', 't.name', 't.slug', 'tt.description')
                ->orderBy('t.name')
                ->get();

            $valSortOrder = 0;
            foreach ($values as $val) {
                $attrValue = ProductAttributeValue::updateOrCreate(
                    ['product_attribute_id' => $attribute->id, 'slug' => $val->slug],
                    [
                        'value' => $val->name,
                        'sort_order' => $valSortOrder++,
                    ],
                );
                $this->attributeValueMap[$taxonomy . ':' . $val->slug] = $attrValue->id;
            }
        }

        $this->info("  Imported {$wpAttributes->count()} attributes.");
    }

    private function importProducts(): void
    {
        $this->info('Importing products...');

        $wpProducts = DB::connection($this->conn)
            ->table('posts')
            ->where('post_type', 'product')
            ->where('post_status', 'publish')
            ->select('ID', 'post_title', 'post_name', 'post_content', 'post_excerpt', 'post_date')
            ->get();

        $count = 0;

        foreach ($wpProducts as $wpProduct) {
            $meta = $this->getPostMeta($wpProduct->ID);

            $categoryTermIds = DB::connection($this->conn)
                ->table('term_relationships as tr')
                ->join('term_taxonomy as tt', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
                ->where('tr.object_id', $wpProduct->ID)
                ->where('tt.taxonomy', 'product_cat')
                ->pluck('tt.term_id')
                ->toArray();

            $categoryId = null;
            foreach ($categoryTermIds as $termId) {
                if (isset($this->categoryMap[$termId])) {
                    $categoryId = $this->categoryMap[$termId];
                    break;
                }
            }

            $price = (float) ($meta['_regular_price'] ?? $meta['_price'] ?? 0);
            $salePrice = $meta['_sale_price'] ?? null;
            $oldPrice = ($salePrice && $salePrice < $price) ? $price : null;
            $actualPrice = $salePrice ?: $price;

            $product = Product::updateOrCreate(
                ['slug' => $wpProduct->post_name],
                [
                    'category_id' => $categoryId,
                    'name' => $wpProduct->post_title,
                    'sku' => $meta['_sku'] ?? null,
                    'short_description' => $wpProduct->post_excerpt ?: null,
                    'description' => $wpProduct->post_content ?: null,
                    'price' => $actualPrice ?: 0,
                    'old_price' => $oldPrice,
                    'stock' => (int) ($meta['_stock'] ?? 0),
                    'is_active' => ($meta['_stock_status'] ?? 'instock') === 'instock',
                ],
            );

            $laravelCategoryIds = array_filter(array_map(
                fn ($termId) => $this->categoryMap[$termId] ?? null,
                $categoryTermIds,
            ));
            $product->categories()->sync($laravelCategoryIds);

            $this->productMap[$wpProduct->ID] = $product->id;

            $this->importProductSpecifications($wpProduct->ID, $product->id);

            $count++;
        }

        $this->info("  Imported {$count} products.");
    }

    private function importProductSpecifications(int $wpPostId, int $productId): void
    {
        $meta = $this->getPostMeta($wpPostId);
        $productAttributes = isset($meta['_product_attributes'])
            ? @unserialize($meta['_product_attributes'])
            : null;

        if (! is_array($productAttributes)) {
            return;
        }

        $sortOrder = 0;
        foreach ($productAttributes as $key => $attrData) {
            if (! is_array($attrData)) {
                continue;
            }

            $isVariation = (int) ($attrData['is_variation'] ?? 0);
            if ($isVariation) {
                continue;
            }

            $name = $attrData['name'] ?? $key;
            $value = $attrData['value'] ?? '';

            if (! $value) {
                continue;
            }

            ProductSpecification::updateOrCreate(
                ['product_id' => $productId, 'name' => $name],
                [
                    'value' => $value,
                    'sort_order' => $sortOrder++,
                ],
            );
        }
    }

    private function importVariations(): void
    {
        $this->info('Importing product variations...');

        $wpVariations = DB::connection($this->conn)
            ->table('posts')
            ->where('post_type', 'product_variation')
            ->where('post_status', 'publish')
            ->select('ID', 'post_parent', 'post_title', 'post_name', 'menu_order')
            ->orderBy('post_parent')
            ->orderBy('menu_order')
            ->get();

        $count = 0;

        foreach ($wpVariations as $wpVar) {
            $productId = $this->productMap[$wpVar->post_parent] ?? null;
            if (! $productId) {
                continue;
            }

            $meta = $this->getPostMeta($wpVar->ID);

            $price = (float) ($meta['_regular_price'] ?? $meta['_price'] ?? 0);
            $salePrice = $meta['_sale_price'] ?? null;
            $oldPrice = ($salePrice && $salePrice < $price) ? $price : null;
            $actualPrice = $salePrice ?: $price;

            $variant = ProductVariant::create([
                'product_id' => $productId,
                'sku' => $meta['_sku'] ?? null,
                'price' => $actualPrice ?: 0,
                'old_price' => $oldPrice,
                'stock' => (int) ($meta['_stock'] ?? 0),
                'is_active' => ($meta['_stock_status'] ?? 'instock') === 'instock',
                'sort_order' => $wpVar->menu_order,
            ]);

            foreach ($meta as $metaKey => $metaValue) {
                if (! str_starts_with($metaKey, 'attribute_pa_')) {
                    continue;
                }

                $taxonomy = str_replace('attribute_', '', $metaKey);
                $attrSlug = str_replace('pa_', '', $taxonomy);

                $attributeId = $this->attributeMap[$attrSlug] ?? null;
                $valueId = $this->attributeValueMap[$taxonomy . ':' . $metaValue] ?? null;

                if ($attributeId && $valueId) {
                    DB::table('product_variant_attributes')->insert([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $attributeId,
                        'product_attribute_value_id' => $valueId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $count++;
        }

        $this->info("  Imported {$count} variations.");
    }

    private function importImages(): void
    {
        $this->info('Importing product images...');

        $count = 0;
        $shouldDownload = $this->option('images');

        foreach ($this->productMap as $wpProductId => $productId) {
            $meta = $this->getPostMeta($wpProductId);

            $thumbnailId = $meta['_thumbnail_id'] ?? null;
            $galleryIds = $meta['_product_image_gallery'] ?? '';
            $galleryIdArray = array_filter(explode(',', $galleryIds));

            $allImageIds = array_filter(array_unique(
                array_merge(
                    $thumbnailId ? [(int) $thumbnailId] : [],
                    array_map('intval', $galleryIdArray),
                ),
            ));

            $sortOrder = 0;
            foreach ($allImageIds as $attachmentId) {
                $attachment = DB::connection($this->conn)
                    ->table('posts')
                    ->where('ID', $attachmentId)
                    ->where('post_type', 'attachment')
                    ->first();

                if (! $attachment) {
                    continue;
                }

                $imageUrl = $attachment->guid;
                $alt = DB::connection($this->conn)
                    ->table('postmeta')
                    ->where('post_id', $attachmentId)
                    ->where('meta_key', '_wp_attachment_metadata')
                    ->value('meta_value');

                $localPath = $imageUrl;

                if ($shouldDownload && $imageUrl) {
                    $localPath = $this->downloadImage($imageUrl, $productId);
                }

                ProductImage::updateOrCreate(
                    ['product_id' => $productId, 'sort_order' => $sortOrder],
                    [
                        'path' => $localPath,
                        'alt' => $attachment->post_title ?: null,
                    ],
                );

                $sortOrder++;
                $count++;
            }
        }

        $this->info("  Imported {$count} images.");
    }

    private function downloadImage(string $url, int $productId): string
    {
        try {
            $response = Http::timeout(30)->get($url);

            if (! $response->successful()) {
                $this->warn("  Failed to download: {$url}");
                return $url;
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = Str::random(20) . '.' . $extension;
            $storagePath = "products/{$productId}/{$filename}";

            Storage::disk('public')->put($storagePath, $response->body());

            return $storagePath;
        } catch (\Throwable $e) {
            $this->warn("  Failed to download {$url}: {$e->getMessage()}");
            return $url;
        }
    }

    private function importBlogPosts(): void
    {
        $this->info('Importing blog posts...');

        $wpPosts = DB::connection($this->conn)
            ->table('posts')
            ->where('post_type', 'post')
            ->where('post_status', 'publish')
            ->select('ID', 'post_title', 'post_name', 'post_content', 'post_excerpt', 'post_date')
            ->get();

        foreach ($wpPosts as $wpPost) {
            $meta = $this->getPostMeta($wpPost->ID);
            $thumbnailId = $meta['_thumbnail_id'] ?? null;
            $featuredImage = null;

            if ($thumbnailId) {
                $attachment = DB::connection($this->conn)
                    ->table('posts')
                    ->where('ID', $thumbnailId)
                    ->where('post_type', 'attachment')
                    ->first();

                $featuredImage = $attachment?->guid;
            }

            Post::updateOrCreate(
                ['slug' => $wpPost->post_name],
                [
                    'title' => $wpPost->post_title,
                    'excerpt' => $wpPost->post_excerpt ?: null,
                    'content' => $wpPost->post_content ?: null,
                    'featured_image' => $featuredImage,
                    'published_at' => $wpPost->post_date,
                    'is_published' => true,
                ],
            );
        }

        $this->info("  Imported {$wpPosts->count()} blog posts.");
    }

    private function importPages(): void
    {
        $this->info('Importing pages...');

        $wpPages = DB::connection($this->conn)
            ->table('posts')
            ->where('post_type', 'page')
            ->where('post_status', 'publish')
            ->select('ID', 'post_title', 'post_name', 'post_content', 'post_date')
            ->get();

        foreach ($wpPages as $wpPage) {
            Page::updateOrCreate(
                ['slug' => $wpPage->post_name],
                [
                    'title' => $wpPage->post_title,
                    'content' => $wpPage->post_content ?: null,
                    'is_published' => true,
                ],
            );
        }

        $this->info("  Imported {$wpPages->count()} pages.");
    }

    private function importOrders(): void
    {
        $this->info('Importing orders...');

        $wpOrders = DB::connection($this->conn)
            ->table('wc_orders')
            ->where('type', 'shop_order')
            ->select('id', 'status', 'currency', 'total_amount', 'customer_id',
                'billing_email', 'date_created_gmt', 'date_updated_gmt',
                'payment_method', 'payment_method_title', 'customer_note')
            ->get();

        $count = 0;

        foreach ($wpOrders as $wpOrder) {
            $billingAddress = DB::connection($this->conn)
                ->table('wc_order_addresses')
                ->where('order_id', $wpOrder->id)
                ->where('address_type', 'billing')
                ->first();

            $shippingAddress = DB::connection($this->conn)
                ->table('wc_order_addresses')
                ->where('order_id', $wpOrder->id)
                ->where('address_type', 'shipping')
                ->first();

            $address = $shippingAddress ?? $billingAddress;

            $statusMap = [
                'wc-completed' => Order::STATUS_COMPLETED,
                'wc-processing' => Order::STATUS_PROCESSING,
                'wc-on-hold' => Order::STATUS_PROCESSING,
                'wc-pending' => Order::STATUS_NEW,
                'wc-cancelled' => Order::STATUS_CANCELLED,
                'wc-refunded' => Order::STATUS_CANCELLED,
                'wc-failed' => Order::STATUS_CANCELLED,
            ];

            $paymentStatusMap = [
                'wc-completed' => Order::PAYMENT_PAID,
                'wc-processing' => Order::PAYMENT_PAID,
                'wc-on-hold' => Order::PAYMENT_PENDING,
                'wc-pending' => Order::PAYMENT_PENDING,
                'wc-cancelled' => Order::PAYMENT_FAILED,
                'wc-refunded' => Order::PAYMENT_FAILED,
                'wc-failed' => Order::PAYMENT_FAILED,
            ];

            $order = Order::create([
                'order_number' => 'WP-' . $wpOrder->id,
                'user_id' => null,
                'customer_first_name' => $billingAddress->first_name ?? '',
                'customer_last_name' => $billingAddress->last_name ?? '',
                'customer_email' => $wpOrder->billing_email ?? $billingAddress->email ?? '',
                'customer_phone' => $billingAddress->phone ?? '',
                'customer_address_line_1' => $address->address_1 ?? '',
                'customer_address_line_2' => $address->address_2 ?? null,
                'customer_city' => $address->city ?? '',
                'customer_region' => $address->state ?? null,
                'customer_postal_code' => $address->postcode ?? null,
                'customer_country' => $address->country ?? 'RU',
                'subtotal' => (float) $wpOrder->total_amount,
                'shipping_total' => 0,
                'total' => (float) $wpOrder->total_amount,
                'payment_method' => $wpOrder->payment_method ?: 'cash_on_delivery',
                'payment_status' => $paymentStatusMap[$wpOrder->status] ?? Order::PAYMENT_PENDING,
                'status' => $statusMap[$wpOrder->status] ?? Order::STATUS_NEW,
                'comment' => $wpOrder->customer_note ?: null,
                'created_at' => $wpOrder->date_created_gmt,
                'updated_at' => $wpOrder->date_updated_gmt,
            ]);

            $this->importOrderItems($wpOrder->id, $order);

            $count++;
        }

        $this->info("  Imported {$count} orders.");
    }

    private function importOrderItems(int $wpOrderId, Order $order): void
    {
        $wpItems = DB::connection($this->conn)
            ->table('woocommerce_order_items')
            ->where('order_id', $wpOrderId)
            ->where('order_item_type', 'line_item')
            ->get();

        foreach ($wpItems as $wpItem) {
            $itemMeta = DB::connection($this->conn)
                ->table('woocommerce_order_itemmeta')
                ->where('order_item_id', $wpItem->order_item_id)
                ->pluck('meta_value', 'meta_key')
                ->toArray();

            $wpProductId = (int) ($itemMeta['_product_id'] ?? 0);
            $productId = $this->productMap[$wpProductId] ?? null;
            $qty = (int) ($itemMeta['_qty'] ?? 1);
            $lineTotal = (float) ($itemMeta['_line_total'] ?? 0);
            $price = $qty > 0 ? $lineTotal / $qty : 0;

            $order->items()->create([
                'product_id' => $productId,
                'name' => $wpItem->order_item_name,
                'sku' => $itemMeta['_sku'] ?? null,
                'price' => $price,
                'quantity' => $qty,
                'line_total' => $lineTotal,
            ]);
        }

        $shippingItems = DB::connection($this->conn)
            ->table('woocommerce_order_items')
            ->where('order_id', $wpOrderId)
            ->where('order_item_type', 'shipping')
            ->get();

        foreach ($shippingItems as $shippingItem) {
            $shippingMeta = DB::connection($this->conn)
                ->table('woocommerce_order_itemmeta')
                ->where('order_item_id', $shippingItem->order_item_id)
                ->pluck('meta_value', 'meta_key')
                ->toArray();

            $shippingTotal = (float) ($shippingMeta['cost'] ?? 0);
            $order->update([
                'shipping_total' => $shippingTotal,
                'total' => (float) $order->subtotal + $shippingTotal,
            ]);
        }
    }

    private function getPostMeta(int $postId): array
    {
        return DB::connection($this->conn)
            ->table('postmeta')
            ->where('post_id', $postId)
            ->pluck('meta_value', 'meta_key')
            ->toArray();
    }
}
