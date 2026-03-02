<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $newOrders = Order::query()->where('status', Order::STATUS_NEW)->count();
        $processingOrders = Order::query()->where('status', Order::STATUS_PROCESSING)->count();
        $totalSales = (float) Order::query()->where('status', Order::STATUS_COMPLETED)->sum('total');
        $totalProducts = Product::query()->active()->count();
        $unreadMessages = ContactSubmission::query()->unread()->count();

        return [
            Stat::make('Новые заказы', (string) $newOrders)
                ->color($newOrders > 0 ? 'warning' : 'success'),
            Stat::make('В обработке', (string) $processingOrders)
                ->color($processingOrders > 0 ? 'info' : 'success'),
            Stat::make('Выручка (выполненные)', number_format($totalSales, 0, '', ' ') . ' ₽'),
            Stat::make('Товаров в каталоге', (string) $totalProducts),
            Stat::make('Непрочитанных обращений', (string) $unreadMessages)
                ->color($unreadMessages > 0 ? 'danger' : 'success'),
        ];
    }
}
