<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')
                    ->schema([
                        TextInput::make('code')
                            ->label('Код купона')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(2),
                        Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ]),

                Section::make('Тип и значение')
                    ->schema([
                        Select::make('type')
                            ->label('Тип скидки')
                            ->options([
                                'fixed' => 'Фиксированная сумма (₽)',
                                'percent' => 'Процент (%)',
                                'free_product' => 'Бесплатный товар (подарок)',
                            ])
                            ->required()
                            ->live(),
                        TextInput::make('value')
                            ->label('Значение скидки')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->requiredIf('type', ['fixed', 'percent'])
                            ->visible(fn ($get) => in_array($get('type'), ['fixed', 'percent', null])),
                        TextInput::make('max_discount')
                            ->label('Макс. скидка (₽)')
                            ->helperText('Потолок скидки для процентных купонов')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->visible(fn ($get) => $get('type') === 'percent'),
                        Select::make('free_product_id')
                            ->label('Бесплатный товар')
                            ->options(fn () => Product::query()->where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->visible(fn ($get) => $get('type') === 'free_product'),
                    ]),

                Section::make('Область действия')
                    ->schema([
                        Select::make('scope')
                            ->label('Применяется к')
                            ->options([
                                'cart' => 'Вся корзина',
                                'products' => 'Конкретные товары',
                                'categories' => 'Категории товаров',
                            ])
                            ->default('cart')
                            ->required()
                            ->live(),
                        Select::make('products')
                            ->label('Товары')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('scope') === 'products'),
                        Select::make('categories')
                            ->label('Категории')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('scope') === 'categories'),
                    ]),

                Section::make('Условия')
                    ->schema([
                        TextInput::make('min_order_amount')
                            ->label('Мин. сумма заказа (₽)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01),
                        TextInput::make('usage_limit')
                            ->label('Лимит использований')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('used_count')
                            ->label('Использовано')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        DatePicker::make('active_from')
                            ->label('Действует с'),
                        DatePicker::make('active_until')
                            ->label('Действует до'),
                    ]),
            ]);
    }
}
