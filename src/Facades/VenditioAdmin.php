<?php

namespace PictaStudio\VenditioAdmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PictaStudio\VenditioAdmin\VenditioAdmin
 */
class VenditioAdmin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \PictaStudio\VenditioAdmin\VenditioAdmin::class;
    }
}
