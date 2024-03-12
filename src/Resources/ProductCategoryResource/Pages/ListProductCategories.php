<?php

namespace PictaStudio\VenditioAdmin\Resources\ProductCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductCategories extends ListRecords
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.product_category.class');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
