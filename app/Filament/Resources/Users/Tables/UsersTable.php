<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bonus_balance')
                    ->label('Бонусы')
                    ->numeric(0)
                    ->sortable(),
                TextColumn::make('loyalty_tier')
                    ->label('Уровень')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => '5%',
                        2 => '7%',
                        3 => '10%',
                        4 => '15%',
                        default => (string) $state,
                    })
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'gray',
                        2 => 'info',
                        3 => 'warning',
                        4 => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('total_spent')
                    ->label('Покупки')
                    ->numeric(0)
                    ->sortable(),
                IconColumn::make('is_admin')
                    ->label('Админ')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([])
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
