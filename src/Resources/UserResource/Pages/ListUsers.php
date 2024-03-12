<?php

namespace PictaStudio\VenditioAdmin\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.user.class');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
