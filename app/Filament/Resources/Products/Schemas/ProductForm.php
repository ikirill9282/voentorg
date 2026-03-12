<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Товар')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Общее')
                            ->schema([
                                Select::make('category_id')
                                    ->label('Категория')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('name')
                                    ->label('Название')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('price')
                                    ->label('Цена')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01),
                                TextInput::make('old_price')
                                    ->label('Старая цена')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01),
                                TextInput::make('stock')
                                    ->label('Остаток')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(1)
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label('Активен')
                                    ->default(true),
                                Textarea::make('short_description')
                                    ->label('Короткое описание')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                RichEditor::make('description')
                                    ->label('Описание')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Изображения')
                            ->schema([
                                Repeater::make('images')
                                    ->label('Изображения товара')
                                    ->relationship('images')
                                    ->schema([
                                        Select::make('type')
                                            ->label('Тип')
                                            ->options([
                                                'image' => 'Изображение',
                                                'video' => 'Видео',
                                            ])
                                            ->default('image')
                                            ->reactive(),
                                        TextInput::make('path')
                                            ->label('Путь / URL изображения')
                                            ->required()
                                            ->maxLength(500),
                                        TextInput::make('alt')
                                            ->label('Alt текст')
                                            ->maxLength(255),
                                        TextInput::make('sort_order')
                                            ->label('Сортировка')
                                            ->numeric()
                                            ->default(0),
                                        TextInput::make('video_url')
                                            ->label('URL видео (MP4)')
                                            ->maxLength(500)
                                            ->visible(fn ($get) => $get('type') === 'video'),
                                        TextInput::make('video_thumbnail')
                                            ->label('Миниатюра видео')
                                            ->maxLength(500)
                                            ->visible(fn ($get) => $get('type') === 'video'),
                                        Select::make('orientation')
                                            ->label('Ориентация')
                                            ->options([
                                                'vertical' => 'Вертикальное',
                                                'horizontal' => 'Горизонтальное',
                                            ])
                                            ->visible(fn ($get) => $get('type') === 'video'),
                                    ])
                                    ->columns(4)
                                    ->defaultItems(0)
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Вариации')
                            ->schema([
                                Repeater::make('variants')
                                    ->label('Вариации товара')
                                    ->relationship('variants')
                                    ->schema(self::variantSchema())
                                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                        $variantId = $data['id'] ?? null;
                                        if ($variantId) {
                                            $variant = ProductVariant::with('attributeValues')->find($variantId);
                                            if ($variant) {
                                                foreach ($variant->attributeValues as $attrValue) {
                                                    $attrId = $attrValue->pivot->product_attribute_id;
                                                    $data["pivot_attr_{$attrId}"] = (string) $attrValue->id;
                                                }
                                            }
                                        }

                                        return $data;
                                    })
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Характеристики')
                            ->schema([
                                Repeater::make('specifications')
                                    ->label('Характеристики товара')
                                    ->relationship('specifications')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Название')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('value')
                                            ->label('Значение')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('sort_order')
                                            ->label('Сортировка')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Наличие в магазинах')
                            ->schema([
                                Select::make('store_ids')
                                    ->label('Магазины, где товар в наличии')
                                    ->multiple()
                                    ->options(\App\Models\Store::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull()
                                    ->afterStateHydrated(function ($component, $record) {
                                        if ($record) {
                                            $component->state($record->stores()->where('in_stock', true)->pluck('stores.id')->toArray());
                                        }
                                    })
                                    ->dehydrated(false),
                            ]),
                    ]),
            ]);
    }

    private static function variantSchema(): array
    {
        $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get();

        $schema = [
            TextInput::make('sku')
                ->label('SKU'),
            TextInput::make('price')
                ->label('Цена')
                ->required()
                ->numeric()
                ->minValue(0),
            TextInput::make('old_price')
                ->label('Старая цена')
                ->numeric()
                ->minValue(0),
            TextInput::make('stock')
                ->label('Остаток')
                ->required()
                ->numeric()
                ->minValue(0)
                ->default(0),
            Toggle::make('is_active')
                ->label('Активна')
                ->default(true),
            TextInput::make('sort_order')
                ->label('Сортировка')
                ->numeric()
                ->default(0),
        ];

        if ($attributes->isNotEmpty()) {
            $attrFields = [];
            foreach ($attributes as $attr) {
                $attrFields[] = Select::make("pivot_attr_{$attr->id}")
                    ->label($attr->name)
                    ->options($attr->values->pluck('value', 'id'))
                    ->nullable()
                    ->dehydrated(false);
            }

            $schema[] = Section::make('Атрибуты')
                ->schema($attrFields)
                ->columns(3)
                ->columnSpanFull()
                ->compact();
        }

        return $schema;
    }

    public static function syncVariantAttributes(array $variantsState, $product): void
    {
        $allAttrIds = ProductAttribute::pluck('id');
        $variants = $product->variants()->get();

        foreach ($variantsState as $stateItem) {
            $variantId = $stateItem['id'] ?? null;
            if (! $variantId) {
                continue;
            }

            $variant = $variants->firstWhere('id', (int) $variantId);
            if (! $variant) {
                continue;
            }

            $syncData = [];
            foreach ($allAttrIds as $attrId) {
                $key = "pivot_attr_{$attrId}";
                $valueId = $stateItem[$key] ?? null;
                if ($valueId) {
                    $syncData[(int) $valueId] = ['product_attribute_id' => (int) $attrId];
                }
            }

            $variant->attributeValues()->sync($syncData);
        }
    }
}
