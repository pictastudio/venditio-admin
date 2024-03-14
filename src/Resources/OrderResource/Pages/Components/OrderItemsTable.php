<?php

namespace PictaStudio\VenditioAdmin\Resources\OrderResource\Pages\Components;

use Closure;
use Filament\Forms;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Livewire\Attributes\Computed;
use Lunar\Models\Transaction;
use PictaStudio\VenditioAdmin\Livewire\Components\TableComponent;
use PictaStudio\VenditioAdmin\Support\Tables\Components\KeyValue;
use PictaStudio\VenditioCore\Models\OrderLine;

class OrderItemsTable extends TableComponent
{
    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->lines()->getQuery())
            ->paginated(config('venditio-admin.resources.default.order.configuration.order_lines.paginated'))
            // ->wherein('type', ['physical', 'digital']))
            ->columns([
                Split::make([
                    ImageColumn::make('image')
                        ->defaultImageUrl(fn () => 'data:image/svg+xml;base64, ' . base64_encode(
                            Blade::render('<x-filament::icon icon="heroicon-o-photo" style="color:rgb(' . Color::Gray[400] . ');"/>')
                        ))
                        ->grow(false)
                        ->getStateUsing(function ($record) {
                            $image = $record->product_item['images'][0]['img'] ?? null;

                            if (!$image) {
                                return;
                            }

                            return asset('storage/' . $image);
                        }),

                    Stack::make([
                        Split::make([
                            Stack::make([
                                TextColumn::make('product_name')
                                    ->weight(FontWeight::Bold),
                                TextColumn::make('product_sku'),
                                // TextColumn::make('identifier')
                                //     ->color(Color::Gray),
                                // TextColumn::make('options')
                                //     ->getStateUsing(fn ($record) => $record->purchasable?->getOptions())
                                //     ->badge(),
                            ]),

                            Stack::make([
                                TextColumn::make('unit_final_price')
                                    ->label(__('venditio-admin::translations.order.infolist.unit_final_price.label'))
                                    ->alignEnd()
                                    ->weight(FontWeight::Bold)
                                    ->getStateUsing(fn ($record) => "{$record->qty} x {$record->unit_final_price}"),
                                TextColumn::make('total_final_price')
                                    ->label(__('venditio-admin::translations.order.infolist.total_final_price.label'))
                                    ->alignEnd()
                                    ->color(Color::Gray),
                            ]),
                        ])
                            ->extraAttributes(['style' => 'align-items: start;']),
                    ])
                        ->columnSpanFull(),
                ])->extraAttributes(['style' => 'align-items: start;']),
                Panel::make([
                    Stack::make([
                        KeyValue::make('price_breakdowns')
                            ->getStateUsing(function (OrderLine $record) {
                                return [
                                    __('venditio-admin::translations.order.infolist.unit_price.label') => $record->unit_price,
                                    __('venditio-admin::translations.order.infolist.qty.label') => $record->qty,
                                    __('venditio-admin::translations.order.infolist.unit_final_price_tax.label') => $record->unit_final_price_tax,
                                    __('venditio-admin::translations.order.infolist.unit_final_price_taxable.label') => $record->unit_final_price_taxable,
                                    __('venditio-admin::translations.order.infolist.unit_discount.label') => $record->unit_discount,
                                    __('venditio-admin::translations.order.infolist.total_final_price.label') => $record->total_final_price,
                                ];
                            }),
                    ]),
                ])
                    ->collapsed()
                    ->collapsible(),
            ])
            ->bulkActions([
                // $this->getBulkRefundAction(),
            ]);
    }

    protected function getBulkRefundAction(): BulkAction
    {
        return BulkAction::make('bulk_refund')
            ->label(__('lunarpanel::order.action.refund_payment.label'))
            ->modalSubmitActionLabel(__('lunarpanel::order.action.refund_payment.label'))
            ->icon('heroicon-o-backward')
            ->form(fn () => [
                Forms\Components\Select::make('transaction')
                    ->label(__('lunarpanel::order.form.transaction.label'))
                    ->required()
                    ->default(fn () => $this->charges->first()->id)
                    ->options(fn () => $this->charges
                        ->mapWithKeys(fn ($charge) => [
                            $charge->id => "{$charge->amount->formatted} - {$charge->driver} // {$charge->reference}",
                        ]))
                    ->live(),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->label(__('lunarpanel::order.form.amount.label'))
                    ->suffix(fn () => $this->record->currency->code)
                    ->default(fn () => number_format($this->record->lines()->whereIn('id', $this->selectedTableRecords)->get()->sum('total.value') / $this->record->currency->factor, $this->record->currency->decimal_places, '.', ''))
                    ->live()
                    ->minValue(1)
                    ->numeric(),

                Forms\Components\Textarea::make('notes')
                    ->label(__('lunarpanel::order.form.notes.label'))
                    ->maxLength(255),

                Forms\Components\Toggle::make('confirm')
                    ->label(__('lunarpanel::order.form.confirm.label'))
                    ->helperText(__('lunarpanel::order.form.confirm.hint.refund'))
                    ->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                if ($value !== true) {
                                    $fail(__('lunarpanel::order.form.confirm.alert'));
                                }
                            };
                        },
                    ]),
            ])
            // ->action(function ($data, BulkAction $action) {
            //     $transaction = Transaction::findOrFail($data['transaction']);

            //     $response = $transaction->refund(bcmul($data['amount'], $this->record->currency->factor), $data['notes']);

            //     if (! $response->success) {
            //         $action->failureNotification(fn () => $response->message);

            //         $action->failure();

            //         $action->halt();

            //         return;
            //     }

            //     $action->success();
            // })
            ->deselectRecordsAfterCompletion()
            ->successNotificationTitle(__('lunarpanel::order.action.refund_payment.notification.success'))
            ->failureNotificationTitle(__('lunarpanel::order.action.refund_payment.notification.error'))
            ->color('warning');
        // ->visible($this->charges->count() && $this->canBeRefunded);
    }

    // #[Computed]
    // public function charges(): \Illuminate\Support\Collection
    // {
    //     return $this->record->transactions()->whereType('capture')->whereSuccess(true)->get();
    // }

    // #[Computed]
    // public function refunds(): \Illuminate\Support\Collection
    // {
    //     return $this->record->transactions()->whereType('refund')->whereSuccess(true)->get();
    // }

    // #[Computed]
    // public function availableToRefund(): float
    // {
    //     return $this->charges->sum('amount.value') - $this->refunds->sum('amount.value');
    // }

    // #[Computed]
    // public function canBeRefunded(): bool
    // {
    //     return $this->availableToRefund > 0;
    // }
}
