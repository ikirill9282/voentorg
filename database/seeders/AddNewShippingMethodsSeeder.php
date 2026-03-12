<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class AddNewShippingMethodsSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['code' => 'ozon', 'name' => 'OZON Доставка', 'description' => 'Доставка через OZON до адреса', 'price' => 0, 'is_active' => true, 'sort_order' => 50],
            ['code' => 'yandex', 'name' => 'Яндекс Доставка', 'description' => 'Доставка Яндекс до адреса', 'price' => 0, 'is_active' => true, 'sort_order' => 60],
            ['code' => 'pochta', 'name' => 'Почта России', 'description' => 'Доставка Почтой России', 'price' => 0, 'is_active' => true, 'sort_order' => 70],
        ];

        foreach ($methods as $method) {
            ShippingMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
