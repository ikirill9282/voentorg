<?php

namespace App\Filament\Resources\Stores\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                TextInput::make('address')
                    ->label('Адрес')
                    ->maxLength(255),
                TextInput::make('city')
                    ->label('Город')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Телефон')
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Активен')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Сортировка')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
