<?php

namespace App\Filament\Pages;

use App\Models\CommercemlExchangeLog;
use App\Models\Product;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class CommercemlExchange extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Обмен 1С';
    protected static string|\UnitEnum|null $navigationGroup = 'Интеграции';
    protected static ?string $title = 'Обмен с 1С (CommerceML)';
    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.commerceml-exchange';

    public function table(Table $table): Table
    {
        return $table
            ->query(CommercemlExchangeLog::query()->latest())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'catalog' => 'info',
                        'sale' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('mode')
                    ->label('Режим'),
                TextColumn::make('filename')
                    ->label('Файл')
                    ->limit(30),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'error' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('products_created')
                    ->label('Товаров+')
                    ->default(0),
                TextColumn::make('products_updated')
                    ->label('Товаров~')
                    ->default(0),
                TextColumn::make('categories_created')
                    ->label('Категорий+')
                    ->default(0),
                TextColumn::make('orders_exported')
                    ->label('Заказов')
                    ->default(0),
                TextColumn::make('message')
                    ->label('Сообщение')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    public function getStats(): array
    {
        $lastSync = CommercemlExchangeLog::where('status', 'success')
            ->latest()
            ->first();

        $lastError = CommercemlExchangeLog::where('status', 'error')
            ->latest()
            ->first();

        $totalProducts = Product::whereNotNull('external_id')->count();
        $unlinkedProducts = Product::whereNull('external_id')->count();

        return [
            'last_sync' => $lastSync?->created_at?->format('d.m.Y H:i') ?? 'Ещё не было',
            'last_error' => $lastError?->created_at?->format('d.m.Y H:i') ?? 'Нет ошибок',
            'last_error_message' => $lastError?->message ?? '',
            'total_linked' => $totalProducts,
            'total_unlinked' => $unlinkedProducts,
            'enabled' => config('commerceml.enabled'),
            'endpoint' => url('/1c-exchange'),
        ];
    }
}
