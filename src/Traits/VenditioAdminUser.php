<?php

namespace PictaStudio\VenditioAdmin\Traits;

use Filament\Panel;

trait VenditioAdminUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        $allowedRoles = match ($panel->getId()) {
            'venditio-admin' => collect(UserRole::canAccessAdminPanel()) // here use AuthManeger class from the core package
                ->map->value
                ->toArray()
        };

        return $this->hasRole($allowedRoles);
    }
}
