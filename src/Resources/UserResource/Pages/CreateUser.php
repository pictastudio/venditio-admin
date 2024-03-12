<?php

namespace PictaStudio\VenditioAdmin\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.user.class');
    }
}
