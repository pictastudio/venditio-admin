<?php

namespace PictaStudio\VenditioAdmin\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.product.class');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
