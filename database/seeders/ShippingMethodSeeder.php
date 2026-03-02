<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Самовывоз',
                'code' => 'pickup',
                'description' => 'Самовывоз с нашего склада',
                'price' => 0,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Бесплатная доставка (Москва)',
                'code' => 'free_moscow',
                'description' => 'Бесплатная доставка по Москве',
                'price' => 0,
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Бесплатная доставка (вся Россия)',
                'code' => 'free_russia',
                'description' => 'Бесплатная доставка по всей России',
                'price' => 0,
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Бесплатная доставка (регионы)',
                'code' => 'free_regions',
                'description' => 'Бесплатная доставка в регионы',
                'price' => 0,
                'is_active' => true,
                'sort_order' => 40,
            ],
        ];

        foreach ($methods as $method) {
            ShippingMethod::updateOrCreate(
                ['code' => $method['code']],
                $method,
            );
        }
    }
}
