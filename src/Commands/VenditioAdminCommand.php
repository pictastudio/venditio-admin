<?php

namespace PictaStudio\VenditioAdmin\Commands;

use Illuminate\Console\Command;

class VenditioAdminCommand extends Command
{
    public $signature = 'venditio-admin';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
