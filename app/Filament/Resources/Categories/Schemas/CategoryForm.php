<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('Родительская категория')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Нет (корневая)'),
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                FileUpload::make('image')
                    ->label('Изображение')
                    ->image()
                    ->disk('public_uploads')
                    ->directory('images/categories')
                    ->visibility('public')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('4:3')
                    ->imageResizeTargetWidth('800')
                    ->imageResizeTargetHeight('600'),
                TextInput::make('sort_order')
                    ->label('Сортировка')
                    ->numeric()
                    ->default(0),
                Textarea::make('description')
                    ->label('Описание')
                    ->rows(5)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Активна')
                    ->default(true),
            ]);
    }
}
