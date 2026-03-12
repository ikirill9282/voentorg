<?php

namespace App\Filament\Resources\CatalogBanners\Pages;

use App\Filament\Resources\CatalogBanners\CatalogBannerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCatalogBanner extends EditRecord
{
    protected static string $resource = CatalogBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
