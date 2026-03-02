<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_pages_are_accessible(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'is_active' => true,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'legacy/assets/example.jpg',
            'alt' => 'Example',
            'sort_order' => 0,
        ]);

        Post::factory()->create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Page::factory()->create([
            'title' => 'Контакты',
            'slug' => 'contacts',
            'is_published' => true,
        ]);

        Page::factory()->create([
            'title' => 'Политика',
            'slug' => 'policy',
            'is_published' => true,
        ]);

        Page::factory()->create([
            'title' => 'Правила торговли',
            'slug' => 'pravila-torgovli',
            'is_published' => true,
        ]);

        Page::factory()->create([
            'title' => 'Способ доставки',
            'slug' => 'sposob-dostavki',
            'is_published' => true,
        ]);

        Page::factory()->create([
            'title' => 'Как сделать заказ',
            'slug' => 'kak-sdelat-zakaz',
            'is_published' => true,
        ]);

        $this->get('/')->assertOk();
        $this->get('/shop')->assertOk();
        $this->get('/product-category/test-category')->assertOk();
        $this->get('/product/test-product')->assertOk();
        $this->get('/nash-blog')->assertOk();
        $this->get('/blog/test-post')->assertOk();
        $this->get('/contacts')->assertOk();
        $this->get('/policy')->assertOk();
        $this->get('/pravila-torgovli')->assertOk();
        $this->get('/sposob-dostavki')->assertOk();
        $this->get('/kak-sdelat-zakaz')->assertOk();
    }
}
