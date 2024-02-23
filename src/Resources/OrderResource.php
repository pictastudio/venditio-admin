<?php

namespace PictaStudio\VenditioAdmin\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioAdmin\Resources\OrderResource\Pages;
use PictaStudio\VenditioAdmin\Resources\OrderResource\Pages\ManageOrder;
use PictaStudio\VenditioCore\Models\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

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
            ->columns(static::getTableColumns())
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
            ->paginated([10, 25, 50, 100])
            ->selectCurrentPageOnly()
            ->deferLoading();
    }

    public static function getTableColumns(): array
    {
        return [
            // TextColumn::make('status')
            //     ->label(__('venditio-admin::translations.order.table.status.label'))
            //     ->formatStateUsing(fn (string $state) => OrderStatus::getLabel($state))
            //     ->color(fn (string $state) => OrderStatus::getColor($state))
            //     ->badge(),
            TextColumn::make('identifier')
                ->label(__('venditio-admin::translations.order.table.identifier.label'))
                ->toggleable()
                ->searchable(),
            TextColumn::make('user_first_name')
                ->label(__('venditio-admin::translations.order.table.user_first_name.label')),
            TextColumn::make('user_last_name')
                ->label(__('venditio-admin::translations.order.table.user_last_name.label')),
            // TextColumn::make('new_customer')
            //     ->label(__('venditio-admin::translations.order.table.new_customer.label'))
            //     ->formatStateUsing(fn (bool $state) => CustomerStatus::getLabel($state))
            //     ->color(fn (bool $state) => CustomerStatus::getColor($state))
            //     ->icon(fn (bool $state) => CustomerStatus::getIcon($state))
            //     ->badge(),
            // TextColumn::make('shippingAddress.postcode')
            //     ->label(__('venditio-admin::translations.order.table.postcode.label')),
            // TextColumn::make('shippingAddress.contact_email')
            //     ->label(__('venditio-admin::translations.order.table.email.label')),
            // TextColumn::make('shippingAddress.contact_phone')
            //     ->label(__('venditio-admin::translations.order.table.phone.label')),
            TextColumn::make('total_final')
                ->label(__('venditio-admin::translations.order.table.total.label'))
                ->formatStateUsing(fn ($state): string => $state->formatted),
            // TextColumn::make('placed_at')
            //     ->label(__('venditio-admin::translations.order.table.date.label'))
            //     ->dateTime(),
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
