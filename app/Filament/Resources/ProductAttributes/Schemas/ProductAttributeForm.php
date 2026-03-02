<?php

namespace App\Filament\Resources\ProductAttributes\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductAttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('group_name')
                    ->label('Группа')
                    ->maxLength(255)
                    ->placeholder('Комплектация')
                    ->helperText('Атрибуты с одинаковой группой отображаются под общим заголовком'),
                TextInput::make('sort_order')
                    ->label('Сортировка')
                    ->numeric()
                    ->default(0),
                Repeater::make('values')
                    ->label('Значения')
                    ->relationship('values')
                    ->schema([
                        TextInput::make('value')
                            ->label('Значение')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('color_hex')
                            ->label('Цвет HEX')
                            ->maxLength(7)
                            ->placeholder('#A03611'),
                        TextInput::make('sort_order')
                            ->label('Сортировка')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(4)
                    ->defaultItems(0)
                    ->reorderable()
                    ->columnSpanFull(),
            ]);
    }
}
