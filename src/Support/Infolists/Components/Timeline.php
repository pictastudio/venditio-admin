<?php

namespace PictaStudio\VenditioAdmin\Support\Infolists\Components;

use Filament\Infolists\Components\Entry;

class Timeline extends Entry
{
    protected string $view = 'venditio-admin::infolists.components.timeline';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();
    }
}
