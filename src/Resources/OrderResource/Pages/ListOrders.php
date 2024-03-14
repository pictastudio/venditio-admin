<?php

namespace PictaStudio\VenditioAdmin\Resources\OrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use PictaStudio\VenditioAdmin\Resources\Traits\ListWithTabs;

class ListOrders extends ListRecords
{
    use ListWithTabs;

    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.order.class');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    private function getTabsEnum(): string
    {
        return config('venditio-core.orders.status_enum');
    }

    private function getTabsColumn(): string
    {
        return 'status';
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->paginate($this->getTableRecordsPerPage());
        // return $query->simplePaginate($this->getTableRecordsPerPage());
    }
}
