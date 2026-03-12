<?php

namespace App\Filament\Resources\CatalogBanners\Pages;

use App\Filament\Resources\CatalogBanners\CatalogBannerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCatalogBanners extends ListRecords
{
    protected static string $resource = CatalogBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
