<?php

namespace PictaStudio\VenditioAdmin\Support\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;
use Livewire\Mechanisms\ComponentRegistry;

class Livewire extends Entry
{
    protected string $view = 'venditio-admin::infolists.components.livewire';

    protected string $livewireComponent;

    protected ?Closure $configureComponentUsing = null;

    public function content(string $livewireComponent): static
    {
        $this->livewireComponent = $livewireComponent;

        return $this;
    }

    public function getContent()
    {
        return $this->getContentName();
    }

    public function getContentName(): string
    {
        return app(ComponentRegistry::class)->getName($this->livewireComponent);
    }
}
