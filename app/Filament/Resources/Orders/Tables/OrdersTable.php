<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Номер')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Итого')
                    ->money('RUB')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->colors([
                        'warning' => Order::STATUS_NEW,
                        'info' => Order::STATUS_PROCESSING,
                        'success' => Order::STATUS_COMPLETED,
                        'danger' => Order::STATUS_CANCELLED,
                    ]),
                TextColumn::make('payment_status')
                    ->label('Оплата')
                    ->badge()
                    ->colors([
                        'warning' => Order::PAYMENT_PENDING,
                        'success' => Order::PAYMENT_PAID,
                        'danger' => Order::PAYMENT_FAILED,
                    ]),
                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Order::STATUS_NEW => Order::STATUS_NEW,
                        Order::STATUS_PROCESSING => Order::STATUS_PROCESSING,
                        Order::STATUS_COMPLETED => Order::STATUS_COMPLETED,
                        Order::STATUS_CANCELLED => Order::STATUS_CANCELLED,
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        Order::PAYMENT_PENDING => Order::PAYMENT_PENDING,
                        Order::PAYMENT_PAID => Order::PAYMENT_PAID,
                        Order::PAYMENT_FAILED => Order::PAYMENT_FAILED,
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
