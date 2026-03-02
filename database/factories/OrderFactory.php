<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 2000, 45000);
        $shipping = fake()->randomFloat(2, 0, 1000);

        return [
            'order_number' => 'ORD-'.now()->format('Ymd').'-'.strtoupper(fake()->bothify('??###')),
            'user_id' => User::factory(),
            'customer_first_name' => fake()->firstName(),
            'customer_last_name' => fake()->lastName(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_address_line_1' => fake()->streetAddress(),
            'customer_address_line_2' => fake()->optional()->secondaryAddress(),
            'customer_city' => fake()->city(),
            'customer_region' => fake()->state(),
            'customer_postal_code' => fake()->postcode(),
            'customer_country' => 'RU',
            'shipping_method_id' => ShippingMethod::factory(),
            'subtotal' => $subtotal,
            'shipping_total' => $shipping,
            'total' => $subtotal + $shipping,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',
            'status' => 'new',
            'comment' => fake()->optional()->sentence(),
        ];
    }
}
