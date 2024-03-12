<?php

namespace PictaStudio\VenditioAdmin\Resources\OrderResource\Pages;

use BackedEnum;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use PictaStudio\VenditioAdmin\Resources\OrderResource;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $statuses = collect(
            config('venditio-core.orders.status_enum')::cases()
        );

        return [
            'all' => Tab::make('All'),
            ...$statuses->mapWithKeys(
                fn (BackedEnum&HasLabel&HasIcon $status) => [
                    $status->value => Tab::make($status->getLabel())
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status))
                        ->icon($status->getIcon())
                        ->badge(
                            fn () => $this->getResource()::getModel()::where('status', $status)->count()
                        ),
                ]
            ),
        ];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate($this->getTableRecordsPerPage());
    }

    // public function getMaxContentWidth(): MaxWidth
    // {
    //     return MaxWidth::Full;
    // }
}
