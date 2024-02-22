<?php

namespace PictaStudio\VenditioAdmin\Resources\OrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use PictaStudio\VenditioAdmin\Resources\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
