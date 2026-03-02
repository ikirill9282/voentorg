<?php

namespace App\Filament\Resources\DeliveryCompanies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DeliveryCompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Код')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Select::make('shipping_method_id')
                    ->label('Метод доставки')
                    ->relationship('shippingMethod', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Не привязан'),
                TextInput::make('logo')
                    ->label('Логотип (путь)')
                    ->maxLength(500),
                TextInput::make('sort_order')
                    ->label('Сортировка')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Активна')
                    ->default(true),
            ]);
    }
}
