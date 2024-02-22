<?php

namespace PictaStudio\VenditioAdmin\Resources\OrderResource\Pages\Components;

use Closure;
use Filament\Forms;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Computed;
use Lunar\Models\Transaction;
use PictaStudio\VenditioAdmin\Livewire\Components\TableComponent;
use PictaStudio\VenditioAdmin\Support\Tables\Components\KeyValue;

/**
 * @property \Illuminate\Support\Collection $charges
 * @property \Illuminate\Support\Collection $refunds
 * @property float $availableToRefund
 * @property bool $canBeRefunded
 */
class OrderItemsTable extends TableComponent
{
    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->lines()->getQuery())
                // ->wherein('type', ['physical', 'digital']))
            ->columns([
                Tables\Columns\Layout\Split::make([
                    // Tables\Columns\ImageColumn::make('image')
                    //     ->defaultImageUrl(fn () => 'data:image/svg+xml;base64, '.base64_encode(
                    //         Blade::render('<x-filament::icon icon="heroicon-o-photo" style="color:rgb('.Color::Gray[400].');"/>')
                    //     ))
                    //     ->grow(false)
                    //     ->getStateUsing(fn ($record) => $record->purchasable?->getThumbnail()?->getUrl('small')),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\Layout\Stack::make([
                                Tables\Columns\TextColumn::make('description')
                                    ->weight(FontWeight::Bold),
                                Tables\Columns\TextColumn::make('identifier')
                                    ->color(Color::Gray),
                                // Tables\Columns\TextColumn::make('options')
                                //     ->getStateUsing(fn ($record) => $record->purchasable?->getOptions())
                                //     ->badge(),
                            ]),

                            Tables\Columns\Layout\Stack::make([
                                Tables\Columns\TextColumn::make('unit')
                                    ->alignEnd()
                                    ->color(Color::Gray)
                                    ->getStateUsing(fn ($record) => "{$record->qty} @ {$record->unit_price->formatted}"),
                                Tables\Columns\TextColumn::make('sub_total')
                                    ->alignEnd()
                                    ->weight(FontWeight::Bold)
                                    ->formatStateUsing(fn ($state) => $state->formatted),
                            ]),
                        ])
                            ->extraAttributes(['style' => 'align-items: start;']),
                    ])
                        ->columnSpanFull(),
                ])->extraAttributes(['style' => 'align-items: start;']),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        // Tables\Columns\TextColumn::make('stock')
                        //     ->getStateUsing(fn ($record) => $record->purchasable?->stock)
                        //     ->formatStateUsing(fn ($state) => __('lunarpanel::order.infolist.current_stock_level.message', [
                        //         'count' => $state,
                        //     ]))
                        //     ->colors(fn () => [
                        //         'danger' => fn ($state) => $state < 50,
                        //         'success' => fn ($state) => $state >= 50,
                        //     ]),
                        // Tables\Columns\TextColumn::make('meta.stock_level')
                        //     ->formatStateUsing(fn ($state) => __('lunarpanel::order.infolist.purchase_stock_level.message', [
                        //         'count' => $state,
                        //     ]))
                        //     ->color(Color::Gray),
                        // Tables\Columns\TextColumn::make('notes')
                        //     ->description(new HtmlString('<b>'.__('lunarpanel::order.infolist.notes.label').'</b>'), 'above'),

                        KeyValue::make('price_breakdowns')
                            ->getStateUsing(function ($record) {

                                $states = [];

                                $states['unit_price'] = "{$record->unit_price->formatted} / {$record->qty}";
                                $states['qty'] = $record->qty;
                                $states['unit_final_price_taxable'] = $record->unit_final_price_taxable?->formatted;
                                $states['unit_discount'] = $record->unit_discount?->formatted;
                                $states['unit_final_price_tax'] = $record->unit_final_price_tax?->formatted;

                                // foreach ($record->tax_breakdown?->amounts ?? [] as $tax) {
                                //     $states[$tax->description] = $tax->price->formatted;
                                // }

                                $states['total_final_price'] = $record->total_final_price?->formatted;

                                return $states;
                            }),
                    ]),
                ])
                    ->collapsed()
                    ->collapsible(),
            ])
            ->bulkActions([
                $this->getBulkRefundAction(),
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
