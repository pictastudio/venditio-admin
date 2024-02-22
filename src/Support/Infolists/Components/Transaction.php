<?php

namespace PictaStudio\VenditioAdmin\Support\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\File;

class Transaction extends Entry
{
    protected string $view = 'venditio-admin::infolists.components.transaction';

    protected function setUp(): void
    {
        parent::setUp();

        $this->statePath(null);
    }

    public function renderPaymentIcons()
    {
        echo File::get(__DIR__ . '/../../../../resources/icons/payment_icons.svg');
    }
}
