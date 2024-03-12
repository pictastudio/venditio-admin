<?php

namespace PictaStudio\VenditioAdmin\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.user.class');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
