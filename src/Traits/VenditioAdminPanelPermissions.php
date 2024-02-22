<?php

namespace PictaStudio\VenditioAdmin\Traits;

use Filament\Panel;
use PictaStudio\VenditioAdmin\Managers\AuthManager;

trait VenditioAdminPanelPermissions
{
    public function canAccessPanel(Panel $panel): bool
    {
        $allowedRoles = match ($panel->getId()) {
            'venditio-admin' => AuthManager::make($this)->canAccessAdminPanel(),
        };

        return $this->hasRole($allowedRoles);
    }
}
