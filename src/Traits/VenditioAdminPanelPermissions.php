<?php

namespace PictaStudio\VenditioAdmin\Traits;

use Filament\Panel;
use PictaStudio\VenditioCore\Managers\Contracts\AuthManager;

trait VenditioAdminPanelPermissions
{
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'venditio-admin' => app(AuthManager::class)->canAccessAdminPanel(),
            default => false,
        };
    }
}
