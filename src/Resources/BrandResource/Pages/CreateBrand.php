<?php

namespace PictaStudio\VenditioAdmin\Resources\BrandResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateBrand extends CreateRecord
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.brand.class');
    }
}
