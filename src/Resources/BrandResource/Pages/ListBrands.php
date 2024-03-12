<?php

namespace PictaStudio\VenditioAdmin\Resources\BrandResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.brand.class');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
