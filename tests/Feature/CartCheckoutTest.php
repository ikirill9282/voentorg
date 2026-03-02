<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_order_from_cart(): void
    {
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'slug' => 'checkout-product',
            'stock' => 10,
            'price' => 5000,
            'is_active' => true,
        ]);

        $shippingMethod = ShippingMethod::factory()->create([
            'code' => 'courier',
            'price' => 500,
            'is_active' => true,
        ]);

        $this->post('/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertRedirect();

        $this->patch('/cart/items/'.$product->id, [
            'quantity' => 3,
        ])->assertRedirect();

        $response = $this->post('/checkout/orders', [
            'customer_first_name' => 'Ivan',
            'customer_last_name' => 'Petrov',
            'customer_email' => 'ivan@example.com',
            'customer_phone' => '+79990000000',
            'customer_address_line_1' => 'Lenina 1',
            'customer_city' => 'Moscow',
            'customer_region' => 'Moscow',
            'customer_postal_code' => '101000',
            'customer_country' => 'RU',
            'shipping_method_id' => $shippingMethod->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $response->assertRedirect('/checkout');

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);

        $order = Order::query()->first();

        $this->assertEquals(Order::STATUS_NEW, $order->status);
        $this->assertEquals('15500.00', (string) $order->total);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 7,
        ]);
    }
}
