<?php

namespace PictaStudio\VenditioAdmin\Resources;

use Carbon\CarbonImmutable;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioAdmin\Resources\OrderResource\Pages;
use PictaStudio\VenditioAdmin\Resources\OrderResource\Pages\ManageOrder;
use PictaStudio\VenditioCore\Models\Contracts\Order;

class OrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function getModel(): string
    {
        return app(Order::class)::class;
    }

    public static function getModelLabel(): string
    {
        return __('venditio-admin::translations.order.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('venditio-admin::translations.order.label.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('venditio-admin::translations.global.sections.sales');
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::where('status', '=', 'in-process')->count();
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                config('venditio-admin.resources.default.order.class', static::class)::getTableColumns()
            )
            ->filters([

            ])
            ->actions([
                // EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn (Model $record) => ManageOrder::getUrl(['record' => $record]))
            ->defaultSort('id', 'DESC')
            ->headerActions([
                // Action::make('export_with_dates')
                //     ->label('Export')
                //     ->modalHeading(fn (Component $component) => $component->getLabel())
                //     ->modalSubmitActionLabel('Scarica')
                //     ->color('success')
                //     ->button()
                //     ->icon('heroicon-o-download')
                //     ->form([
                //         Grid::make()->schema([
                //             DatePicker::make('date_on')
                //                 ->default(static::getdefaultExportDates()['date_on'])
                //                 ->label('Date on'),
                //             DatePicker::make('date_off')
                //                 ->default(static::getdefaultExportDates()['date_off'])
                //                 ->label('Date off'),
                //         ]),
                //         Select::make('is_rider')
                //             ->options([
                //                 'rider' => 'Rider',
                //                 'admin' => 'Admin',
                //             ])
                //             ->default('admin')
                //             ->label('Tipo export')
                //     ])
                //     ->action(fn (array $data) => (
                //         Excel::download(new LocationsExport(
                //             CarbonImmutable::parse($data['date_on']),
                //             CarbonImmutable::parse($data['date_off']),
                //             isRider: auth()->user()->isRider()
                //                 ? true
                //                 : (
                //                     auth()->user()->isTrenitalia()
                //                         ? false
                //                         : ($data['is_rider'] === 'rider')
                //                 ),
                //         ), 'report_locations.xlsx', ExcelExcel::XLSX)
                //     )),
            ]);
    }

    // public static function getDefaultExportDates()
    // {
    //     return [
    //         'date_on' => request('tableFilters.date.from') ? CarbonImmutable::parse(request('tableFilters.date.from')) : today()->startOfMonth(),
    //         'date_off' => request('tableFilters.date.until') ? CarbonImmutable::parse(request('tableFilters.date.until')) : today()->endOfMonth(),
    //     ];
    // }

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('identifier')
                ->label(__('venditio-admin::translations.order.table.identifier.label'))
                ->searchable(),
            TextColumn::make('status')
                ->label(__('venditio-admin::translations.order.table.status.label'))
                ->searchable()
                ->badge(),
            TextColumn::make('user_first_name')
                ->label(__('venditio-admin::translations.order.table.user_first_name.label'))
                ->searchable(),
            TextColumn::make('user_last_name')
                ->label(__('venditio-admin::translations.order.table.user_last_name.label'))
                ->searchable(),
            TextColumn::make('user_email')
                ->label(__('venditio-admin::translations.order.table.user_email.label'))
                ->searchable(),
            TextColumn::make('total_final')
                ->label(__('venditio-admin::translations.order.table.total.label')),
            TextColumn::make('approved_at')
                ->label(__('venditio-admin::translations.order.table.approved_at.label'))
                ->dateTime(),
        ];
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            'order' => ManageOrder::route('/{record}'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
