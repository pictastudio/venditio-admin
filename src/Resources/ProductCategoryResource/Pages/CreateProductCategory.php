<?php

namespace PictaStudio\VenditioAdmin\Resources\ProductCategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateProductCategory extends CreateRecord
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.product_category.class');
    }
}
