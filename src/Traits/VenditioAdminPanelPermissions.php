<?php

namespace PictaStudio\VenditioAdmin\Traits;

use Filament\Panel;

trait VenditioAdminPanelPermissions
{
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'venditio-admin' => config('venditio-core.auth.manager')::make($this)->canAccessAdminPanel(),
            default => false,
        };
    }
}
