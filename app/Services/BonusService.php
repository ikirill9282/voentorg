<?php

namespace App\Services;

use App\Models\BonusTransaction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BonusService
{
    private const TIER_THRESHOLDS = [
        1 => 0,
        2 => 50000,
        3 => 100000,
        4 => 200000,
    ];

    private const TIER_PERCENTAGES = [
        1 => 5,
        2 => 7,
        3 => 10,
        4 => 15,
    ];

    public function calculateTier(float $totalSpent): int
    {
        if ($totalSpent >= 200000) {
            return 4;
        }
        if ($totalSpent >= 100000) {
            return 3;
        }
        if ($totalSpent >= 50000) {
            return 2;
        }

        return 1;
    }

    public function tierPercentage(int $tier): int
    {
        return self::TIER_PERCENTAGES[$tier] ?? 5;
    }

    public function nextTierThreshold(int $tier): ?float
    {
        $nextTier = $tier + 1;

        return self::TIER_THRESHOLDS[$nextTier] ?? null;
    }

    public function maxRedeemable(User $user, float $orderTotal): float
    {
        return min((float) $user->bonus_balance, $orderTotal * 0.5);
    }

    public function accrueForOrder(Order $order): void
    {
        if (! $order->user_id) {
            return;
        }

        $user = User::query()->lockForUpdate()->find($order->user_id);
        if (! $user) {
            return;
        }

        // Check if already accrued for this order
        $existing = BonusTransaction::query()
            ->where('order_id', $order->id)
            ->where('type', BonusTransaction::TYPE_ACCRUAL)
            ->exists();

        if ($existing) {
            return;
        }

        $percentage = $this->tierPercentage((int) $user->loyalty_tier);
        $bonusAmount = round((float) $order->total * $percentage / 100, 2);

        if ($bonusAmount <= 0) {
            return;
        }

        $newBalance = round((float) $user->bonus_balance + $bonusAmount, 2);

        BonusTransaction::query()->create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => BonusTransaction::TYPE_ACCRUAL,
            'amount' => $bonusAmount,
            'balance_after' => $newBalance,
            'description' => "Начисление {$percentage}% за заказ {$order->order_number}",
        ]);

        $order->update(['bonus_earned' => $bonusAmount]);

        // Recalculate total_spent and tier
        $this->recalculateTotalSpent($user, $newBalance);
    }

    public function redeemForOrder(User $user, Order $order, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($user, $order, $amount): void {
            $freshUser = User::query()->lockForUpdate()->find($user->id);

            $actualAmount = min($amount, (float) $freshUser->bonus_balance);
            if ($actualAmount <= 0) {
                return;
            }

            $newBalance = round((float) $freshUser->bonus_balance - $actualAmount, 2);

            BonusTransaction::query()->create([
                'user_id' => $freshUser->id,
                'order_id' => $order->id,
                'type' => BonusTransaction::TYPE_REDEMPTION,
                'amount' => -$actualAmount,
                'balance_after' => $newBalance,
                'description' => "Списание за заказ {$order->order_number}",
            ]);

            $freshUser->update(['bonus_balance' => $newBalance]);
        });
    }

    public function reverseForOrder(Order $order): void
    {
        if (! $order->user_id) {
            return;
        }

        DB::transaction(function () use ($order): void {
            $user = User::query()->lockForUpdate()->find($order->user_id);
            if (! $user) {
                return;
            }

            $transactions = BonusTransaction::query()
                ->where('order_id', $order->id)
                ->get();

            if ($transactions->isEmpty()) {
                return;
            }

            $totalReverse = 0;
            foreach ($transactions as $tx) {
                $totalReverse -= (float) $tx->amount; // reverse: subtract accruals, add back redemptions
            }

            $newBalance = round((float) $user->bonus_balance + $totalReverse, 2);
            if ($newBalance < 0) {
                $newBalance = 0;
            }

            BonusTransaction::query()->create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => BonusTransaction::TYPE_ADJUSTMENT,
                'amount' => $totalReverse,
                'balance_after' => $newBalance,
                'description' => "Откат бонусов по отменённому заказу {$order->order_number}",
            ]);

            $this->recalculateTotalSpent($user, $newBalance);
        });
    }

    public function adjustBalance(User $user, float $amount, string $description, ?int $adminId = null): void
    {
        DB::transaction(function () use ($user, $amount, $description, $adminId): void {
            $freshUser = User::query()->lockForUpdate()->find($user->id);
            $newBalance = round((float) $freshUser->bonus_balance + $amount, 2);

            if ($newBalance < 0) {
                $newBalance = 0;
            }

            BonusTransaction::query()->create([
                'user_id' => $freshUser->id,
                'type' => BonusTransaction::TYPE_ADJUSTMENT,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'created_by' => $adminId,
            ]);

            $freshUser->update(['bonus_balance' => $newBalance]);
        });
    }

    public function recalculateTotalSpent(User $user, ?float $newBalance = null): void
    {
        $totalSpent = Order::query()
            ->where('user_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->sum('total');

        $tier = $this->calculateTier((float) $totalSpent);

        $update = [
            'total_spent' => $totalSpent,
            'loyalty_tier' => $tier,
        ];

        if ($newBalance !== null) {
            $update['bonus_balance'] = $newBalance;
        }

        $user->update($update);
    }
}
