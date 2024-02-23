<?php

namespace PictaStudio\VenditioAdmin\Resources\BrandResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use PictaStudio\VenditioAdmin\Resources\BrandResource;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
