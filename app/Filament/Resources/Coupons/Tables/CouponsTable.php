<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CouponsTable
{
    private const TYPE_LABELS = [
        'fixed' => '₽ (фикс.)',
        'percent' => '%',
        'free_product' => 'Подарок',
    ];

    private const SCOPE_LABELS = [
        'cart' => 'Корзина',
        'products' => 'Товары',
        'categories' => 'Категории',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn (string $state) => self::TYPE_LABELS[$state] ?? $state),
                TextColumn::make('value')
                    ->label('Значение')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $record->type === 'free_product' ? '—' : $state),
                TextColumn::make('scope')
                    ->label('Область')
                    ->formatStateUsing(fn (string $state) => self::SCOPE_LABELS[$state] ?? $state),
                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('used_count')
                    ->label('Использовано')
                    ->sortable(),
                TextColumn::make('usage_limit')
                    ->label('Лимит')
                    ->default('∞'),
                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип')
                    ->options(self::TYPE_LABELS),
                SelectFilter::make('scope')
                    ->label('Область')
                    ->options(self::SCOPE_LABELS),
                SelectFilter::make('is_active')
                    ->label('Статус')
                    ->options([
                        '1' => 'Активные',
                        '0' => 'Неактивные',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
