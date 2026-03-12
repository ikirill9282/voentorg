<?php

namespace App\Filament\Resources\CatalogBanners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CatalogBannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Категория')
                    ->default('Все разделы')
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Позиция')
                    ->sortable(),
                TextColumn::make('display_mode')
                    ->label('Режим')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'full' => 'Полная ширина',
                        'double' => 'Двойной',
                        'single' => 'Одинарный',
                        default => $state,
                    }),
                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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
