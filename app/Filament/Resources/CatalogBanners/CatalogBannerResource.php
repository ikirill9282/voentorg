<?php

namespace App\Filament\Resources\CatalogBanners;

use App\Filament\Resources\CatalogBanners\Pages\CreateCatalogBanner;
use App\Filament\Resources\CatalogBanners\Pages\EditCatalogBanner;
use App\Filament\Resources\CatalogBanners\Pages\ListCatalogBanners;
use App\Filament\Resources\CatalogBanners\Schemas\CatalogBannerForm;
use App\Filament\Resources\CatalogBanners\Tables\CatalogBannersTable;
use App\Models\CatalogBanner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CatalogBannerResource extends Resource
{
    protected static ?string $model = CatalogBanner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Каталог';

    protected static ?string $navigationLabel = 'Баннеры каталога';

    protected static ?string $modelLabel = 'Баннер';

    protected static ?string $pluralModelLabel = 'Баннеры каталога';

    public static function form(Schema $schema): Schema
    {
        return CatalogBannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CatalogBannersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCatalogBanners::route('/'),
            'create' => CreateCatalogBanner::route('/create'),
            'edit' => EditCatalogBanner::route('/{record}/edit'),
        ];
    }
}
