<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\BonusService;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    public function __construct(
        private readonly BonusService $bonusService,
    ) {
    }

    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        $newStatus = $order->status;

        if ($newStatus === Order::STATUS_COMPLETED) {
            DB::transaction(fn () => $this->bonusService->accrueForOrder($order));
        }

        if ($newStatus === Order::STATUS_CANCELLED) {
            $this->bonusService->reverseForOrder($order);
        }
    }
}
