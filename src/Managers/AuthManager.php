<?php

namespace PictaStudio\VenditioAdmin\Managers;

use PictaStudio\VenditioCore\Managers\AuthManager as VenditioCoreAuthManager;

class AuthManager extends VenditioCoreAuthManager
{
    public function canAccessAdminPanel(): bool
    {
        return $this->user->hasRole('admin');
    }
}
