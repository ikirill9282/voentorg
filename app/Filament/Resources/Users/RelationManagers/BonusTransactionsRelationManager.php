<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\BonusTransaction;
use App\Services\BonusService;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BonusTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'bonusTransactions';

    protected static ?string $title = 'Бонусные операции';

    protected static ?string $modelLabel = 'Операция';

    protected static ?string $pluralModelLabel = 'Операции';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        BonusTransaction::TYPE_ACCRUAL => 'Начисление',
                        BonusTransaction::TYPE_REDEMPTION => 'Списание',
                        BonusTransaction::TYPE_ADJUSTMENT => 'Корректировка',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        BonusTransaction::TYPE_ACCRUAL => 'success',
                        BonusTransaction::TYPE_REDEMPTION => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label('Сумма')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('balance_after')
                    ->label('Баланс после')
                    ->numeric(2),
                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(50),
                TextColumn::make('order.order_number')
                    ->label('Заказ'),
                TextColumn::make('creator.name')
                    ->label('Автор'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Action::make('adjust')
                    ->label('Ручная корректировка')
                    ->form([
                        TextInput::make('amount')
                            ->label('Сумма (положительная = начисление, отрицательная = списание)')
                            ->numeric()
                            ->required(),
                        TextInput::make('description')
                            ->label('Причина')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        $bonusService = app(BonusService::class);
                        $bonusService->adjustBalance(
                            $this->getOwnerRecord(),
                            (float) $data['amount'],
                            $data['description'],
                            auth()->id(),
                        );
                    }),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
