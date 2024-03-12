<?php

namespace PictaStudio\VenditioAdmin\Resources\ProductResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.product.class');
    }
}
