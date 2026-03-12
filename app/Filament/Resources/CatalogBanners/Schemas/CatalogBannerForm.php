<?php

namespace App\Filament\Resources\CatalogBanners\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CatalogBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->label('Категория (пусто = все разделы)')
                    ->options(Category::query()->active()->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Select::make('display_mode')
                    ->label('Режим отображения')
                    ->options([
                        'full' => 'На всю ширину (3 колонки)',
                        'double' => 'Двойной (2 колонки)',
                        'single' => 'Одинарный (1 колонка)',
                    ])
                    ->default('full')
                    ->required(),
                TextInput::make('position')
                    ->label('Позиция (после какого товара)')
                    ->numeric()
                    ->default(0)
                    ->helperText('0 = в начале, 3 = после 3-го товара'),
                TextInput::make('link_url')
                    ->label('Ссылка (необязательно)')
                    ->url()
                    ->maxLength(500),
                Toggle::make('is_active')
                    ->label('Активен')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Порядок сортировки')
                    ->numeric()
                    ->default(0),
                Repeater::make('images')
                    ->label('Изображения (слайды)')
                    ->schema([
                        TextInput::make('url')
                            ->label('URL изображения')
                            ->required()
                            ->maxLength(1000),
                        TextInput::make('alt')
                            ->label('Alt текст')
                            ->maxLength(255),
                    ])
                    ->defaultItems(1)
                    ->columnSpanFull(),
            ]);
    }
}
