<?php

namespace Database\Seeders;

use App\Models\DeliveryCompany;
use Illuminate\Database\Seeder;

class DeliveryCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'СДЭК',
                'code' => 'cdek',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Деловые Линии',
                'code' => 'delovye_linii',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Яндекс Доставка',
                'code' => 'yandex_delivery',
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Почта России',
                'code' => 'pochta_rossii',
                'is_active' => true,
                'sort_order' => 40,
            ],
        ];

        foreach ($companies as $company) {
            DeliveryCompany::updateOrCreate(
                ['code' => $company['code']],
                $company,
            );
        }
    }
}
