<?php

namespace PictaStudio\VenditioAdmin\Resources\OrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
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

    // public function getTabs(): array
    // {
    //     $statuses = collect(
    //         config('lunar.orders.statuses', [])
    //     )->filter(
    //         fn ($config) => $config['favourite'] ?? false
    //     );

    //     return [
    //         'all' => Tab::make('All'),
    //         ...collect($statuses)->mapWithKeys(
    //             fn ($config, $status) => [
    //                 $status => Tab::make($config['label'])
    //                     ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status)),
    //             ]
    //         ),
    //     ];
    // }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate($this->getTableRecordsPerPage());
    }

    // public function getMaxContentWidth(): MaxWidth
    // {
    //     return MaxWidth::Full;
    // }
}
