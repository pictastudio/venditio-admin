<?php

namespace PictaStudio\VenditioAdmin\Resources\Traits;

use BackedEnum;
use Filament\Resources\Components\Tab;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;

trait ListWithTabs
{
    abstract private function getTabsEnum(): string;

    abstract private function getTabsColumn(): string;

    public function getTabs(): array
    {
        $cases = collect(
            $this->getTabsEnum()::cases()
        );

        return [
            'all' => Tab::make('All')
                ->badge(fn () => $this->getTableQuery()->count()),
            ...$cases->mapWithKeys(
                fn (BackedEnum&HasLabel&HasColor $case) => [
                    $case->value => Tab::make($case->getLabel())
                        ->modifyQueryUsing(fn (Builder $query) => $query->where($this->getTabsColumn(), $case))
                        ->icon(fn () => $case instanceof HasIcon ? $case->getIcon() : null)
                        ->badgeColor($case->getColor())
                        ->badge(fn () => $this->getTableQuery()->where($this->getTabsColumn(), $case)->count()),
                ]
            ),
        ];
    }
}
