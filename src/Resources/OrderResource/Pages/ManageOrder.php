<?php

namespace PictaStudio\VenditioAdmin\Resources\OrderResource\Pages;

use Awcodes\Shout\Components\ShoutEntry;
use Filament\Infolists;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Lunar\Admin\Filament\Resources\CustomerResource;
use Lunar\DataTypes\Price;
use Lunar\Models\Country;
use Lunar\Models\State;
use PictaStudio\VenditioAdmin\OrderStatus;
use PictaStudio\VenditioAdmin\Resources\OrderResource;
use PictaStudio\VenditioAdmin\Resources\OrderResource\Pages\Components\OrderItemsTable;
use PictaStudio\VenditioAdmin\Support\Infolists\Components\Livewire;
use PictaStudio\VenditioAdmin\Support\Infolists\Components\Tags;
use PictaStudio\VenditioAdmin\Support\Infolists\Components\Timeline;
use PictaStudio\VenditioAdmin\Support\Infolists\Components\Transaction as InfolistsTransaction;

class ManageOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected static string $view = 'venditio-admin::resources.order-resource.pages.manage-order';

    public function getBreadcrumb(): string
    {
        return __('venditio-admin::translations.order.breadcrumb.manage');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Order #' . $this->record->identifier;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        // ShoutEntry::make('requires_capture')
                        //     ->type('danger')
                        //     ->content(__('lunarpanel::order.infolist.alert.requires_capture'))
                        //     ->visible(fn () => $this->requiresCapture),
                        // ShoutEntry::make('requires_capture')
                        //     ->state(fn () => $this->paymentStatus)
                        //     ->icon(fn ($state) => match ($state) {
                        //         'refunded' => FilamentIcon::resolve('lunar::exclamation-circle'),
                        //         default => null
                        //     })
                        //     ->color(fn (ShoutEntry $component, $state) => match ($state) {
                        //         'partial-refund' => 'info',
                        //         'refunded' => 'danger',
                        //         default => null
                        //     })->content(fn ($state) => match ($state) {
                        //         'partial-refund' => __('lunarpanel::order.infolist.alert.partially_refunded'),
                        //         'refunded' => __('lunarpanel::order.infolist.alert.refunded') ,
                        //         default => null
                        //     })
                        //     ->visible(fn ($state) => in_array($state, ['partial-refund', 'refunded'])),

                        Livewire::make('lines')
                            ->content(OrderItemsTable::class),

                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\Section::make()
                                    ->schema([
                                        // Infolists\Components\TextEntry::make('shipping_fee_description')
                                        //     ->icon('heroicon-s-truck')
                                        //     ->html()
                                        //     ->iconPosition(IconPosition::Before)
                                        //     ->hiddenLabel(),
                                        Infolists\Components\TextEntry::make('shipping_fee')
                                            ->hiddenLabel()
                                            ->alignEnd()
                                            ->formatStateUsing(fn ($state) => $state->formatted),
                                    ]),
                                Infolists\Components\Grid::make()
                                    ->columns(2)
                                    ->schema([
                                        Infolists\Components\Grid::make()
                                            ->columns(1)
                                            ->columnSpan(1)
                                            ->schema([
                                                // Infolists\Components\TextEntry::make('shippingAddress.delivery_instructions')
                                                //     ->label(__('lunarpanel::order.infolist.delivery_instructions.label'))
                                                //     ->hidden(fn ($state) => blank($state)),
                                                Infolists\Components\TextEntry::make('addresses.shipping.notes')
                                                    ->label(__('venditio-admin::translations.order.infolist.notes.label'))
                                                    ->placeholder(__('venditio-admin::translations.order.infolist.notes.placeholder')),
                                            ]),
                                        Infolists\Components\Grid::make()
                                            ->columns(1)
                                            ->columnSpan(1)
                                            ->schema([
                                                Infolists\Components\TextEntry::make('sub_total')
                                                    ->label(__('venditio-admin::translations.order.infolist.sub_total.label'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->formatStateUsing(fn ($state) => $state->formatted),
                                                Infolists\Components\TextEntry::make('discount_amount')
                                                    ->label(__('venditio-admin::translations.order.infolist.discount_amount.label'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->formatStateUsing(fn ($state) => $state->formatted),
                                                Infolists\Components\TextEntry::make('shipping_fee')
                                                    ->label(__('venditio-admin::translations.order.infolist.shipping_fee.label'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->formatStateUsing(fn ($state) => $state->formatted),
                                                Infolists\Components\TextEntry::make('payment_fee')
                                                    ->label(__('venditio-admin::translations.order.infolist.payment_fee.label'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->formatStateUsing(fn ($state) => $state->formatted),

                                                // Infolists\Components\Group::make()
                                                //     ->statePath('tax_breakdown')
                                                //     ->schema(function ($state) {
                                                //         $taxes = [];
                                                //         foreach ($state->amounts ?? [] as $taxIndex => $tax) {
                                                //             $taxes[] = Infolists\Components\TextEntry::make('tax_'.$taxIndex)
                                                //                 ->label(fn () => $tax->description)
                                                //                 ->inlineLabel()
                                                //                 ->alignEnd()
                                                //                 ->state(fn () => $tax->price->formatted);
                                                //         }

                                                //         return $taxes;
                                                //     }),
                                                Infolists\Components\TextEntry::make('total_final')
                                                    ->label(fn () => new HtmlString('<b>' . __('venditio-admin::translations.order.infolist.total_final.label') . '</b>'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->weight(FontWeight::Bold)
                                                    ->formatStateUsing(fn ($state) => $state->formatted),
                                                // Infolists\Components\TextEntry::make('paid')
                                                //     ->label(fn () => __('lunarpanel::order.infolist.paid.label'))
                                                //     ->inlineLabel()
                                                //     ->alignEnd()
                                                //     ->weight(FontWeight::SemiBold)
                                                //     ->getStateUsing(function ($record) {
                                                //         $paid = $record->transactions()
                                                //             ->whereType('capture')
                                                //             ->get()
                                                //             ->sum('amount.value');

                                                //         return (new Price($paid, $record->currency))->formatted;
                                                //     }),
                                                // Infolists\Components\TextEntry::make('refund')
                                                //     ->label(fn () => __('lunarpanel::order.infolist.refund.label'))
                                                //     ->inlineLabel()
                                                //     ->alignEnd()
                                                //     ->color('warning')
                                                //     ->weight(FontWeight::SemiBold)
                                                //     ->getStateUsing(function ($record) {
                                                //         $paid = $record->transactions()
                                                //             ->whereType('refund')
                                                //             ->get()
                                                //             ->sum('amount.value');

                                                //         return (new Price($paid, $record->currency))->formatted;
                                                //     }),
                                            ]),
                                    ]),
                            ]),

                        // Infolists\Components\Section::make('transactions')
                        //     ->heading(__('lunarpanel::order.infolist.transactions.label'))
                        //     ->compact()
                        //     ->collapsed(fn ($state) => filled($state))
                        //     ->collapsible(fn ($state) => filled($state))
                        //     ->schema([
                        //         Infolists\Components\RepeatableEntry::make('transactions')
                        //             ->hiddenLabel()
                        //             ->placeholder(__('lunarpanel::order.infolist.transactions.placeholder'))
                        //             ->getStateUsing(fn ($record) => $record->transactions->sortByDesc('created_at')->sortBy('id'))
                        //             ->contained(false)
                        //             ->schema([
                        //                 InfolistsTransaction::make('transactions'),
                        //             ]),
                        //     ]),

                        // Infolists\Components\Grid::make()
                        //     ->schema([
                        //         Timeline::make('timeline')
                        //             ->label(__('lunarpanel::order.infolist.timeline.label')),
                        //     ]),

                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('user')
                            ->hidden(fn ($state) => blank($state?->id))
                            ->formatStateUsing(fn ($state) => $state->name)
                            ->weight(FontWeight::SemiBold)
                            ->size(TextEntrySize::Large)
                            ->hiddenLabel()
                            ->suffixAction(fn ($state) => Action::make('view customer')
                                ->color('gray')
                                ->button()
                                ->size(ActionSize::ExtraSmall)
                                ->url(
                                    ''
                                    // CustomerResource::getUrl('edit', ['record' => $state->id]))
                                )),
                        Infolists\Components\Section::make()
                            ->compact()
                            ->inlineLabel()
                            ->schema([
                                // Infolists\Components\TextEntry::make('new_customer')
                                //     ->label(__('lunarpanel::order.infolist.new_returning.label'))
                                //     ->alignEnd()
                                //     ->formatStateUsing(fn ($state) => __('lunarpanel::order.infolist.'.($state ? 'new' : 'returning').'_customer.label')),
                                Infolists\Components\TextEntry::make('status')
                                    ->label(__('venditio-admin::translations.order.infolist.status.label'))
                                    // ->formatStateUsing(fn ($state) => OrderStatus::getLabel($state))
                                    ->alignEnd()
                                    // ->color(fn ($state) => OrderStatus::getColor($state))
                                    ->badge(),
                                Infolists\Components\TextEntry::make('identifier')
                                    ->label(__('venditio-admin::translations.order.infolist.identifier.label'))
                                    ->alignEnd()
                                    ->icon('heroicon-o-clipboard')
                                    ->iconPosition(IconPosition::After)
                                    ->copyable(),
                                // Infolists\Components\TextEntry::make('customer_reference')
                                //     ->label(__('lunarpanel::order.infolist.customer_reference.label'))
                                //     ->alignEnd()
                                //     ->icon('heroicon-o-clipboard')
                                //     ->iconPosition(IconPosition::After)
                                //     ->copyable(),
                                // Infolists\Components\TextEntry::make('channel.name')
                                //     ->label(__('lunarpanel::order.infolist.channel.label'))
                                //     ->alignEnd(),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('venditio-admin::translations.order.infolist.date_created.label'))
                                    ->alignEnd()
                                    ->dateTime('Y-m-d h:i a')
                                    ->visible(fn ($record) => !$record->approved_at),
                                Infolists\Components\TextEntry::make('approved_at')
                                    ->label(__('venditio-admin::translations.order.infolist.date_approved.label'))
                                    ->alignEnd()
                                    ->dateTime('Y-m-d h:i a')
                                    ->placeholder('-'),
                            ]),
                        $this->getOrderAddressInfolistSchema('shipping'),
                        $this->getOrderAddressInfolistSchema('billing'),
                        // Infolists\Components\Section::make('tags')
                        //     ->heading(__('lunarpanel::order.infolist.tags.label'))
                        //     ->headerActions([
                        //         fn ($record) => $this->getEditTagsActions(),
                        //     ])
                        //     ->compact()
                        //     ->schema([
                        //         Tags::make(''),
                        //     ]),

                        // Infolists\Components\Section::make('additional_info')
                        //     ->heading(__('lunarpanel::order.infolist.additional_info.label'))
                        //     ->compact()
                        //     ->statePath('meta')
                        //     ->schema(fn ($state) => blank($state) ? [
                        //         Infolists\Components\TextEntry::make('no_additional_info')
                        //             ->hiddenLabel()
                        //             // ->weight(FontWeight::SemiBold)
                        //             ->getStateUsing(fn () => __('lunarpanel::order.infolist.no_additional_info.label')),
                        //     ] : collect($state)
                        //         ->map(fn ($value, $key) => Infolists\Components\TextEntry::make('meta_'.$key)
                        //             ->state($value)
                        //             ->label($key)
                        //             ->inlineLabel())
                        //         ->toArray()),

                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public function getOrderAddressInfolistSchema(string $type)
    {
        // $sameAsShipping = fn ($record) => $type == 'billing' && $this->isShippingEqualsBilling($record->shippingAddress, $record->billingAddress);

        $getAddress = fn ($record) => match ($type) {
            'billing' => $record->addresses['billing'],
            'shipping' => $record->addresses['shipping'],
            default => null,
        };

        return Infolists\Components\Section::make(__("venditio-admin::translations.order.infolist.{$type}_address.label"))
            // ->statePath($type.'Address')
            ->compact()
            // ->headerActions([
            //     fn ($record) => $this->getEditAddressAction($type)->hidden($sameAsShipping($record)),
            // ])
            ->schema([
                Infolists\Components\TextEntry::make($type . '_address')
                    ->hiddenLabel()
                    ->listWithLineBreaks()
                    ->getStateUsing(function ($record) use ($getAddress) {
                        $address = $getAddress($record);

                        if (!empty($address)) {
                            return collect([
                                'first_name' => $address['first_name'],
                                'last_name' => $address['last_name'],
                                'email' => $address['email'],
                                'sex' => $address['sex'],
                                'phone' => $address['phone'],
                                'vat_number' => $address['vat_number'],
                                'fiscal_code' => $address['fiscal_code'],
                                'company_name' => $address['company_name'],
                                'address_line_1' => $address['address_line_1'],
                                'address_line_2' => $address['address_line_2'],
                                'city' => $address['city'],
                                'state' => $address['state'],
                                'zip' => $address['zip'],
                            ])
                                // ->filter(fn ($value, $key) => filled($value) || in_array($key, [
                                //     'fullName', 'line_one', 'postcode', 'country.name',
                                // ]))
                                ->toArray();
                        }

                        return __('venditio-admin::translations.order.infolist.address_not_set.label');

                    }),
                Infolists\Components\TextEntry::make($type . '_phone')
                    ->hiddenLabel()
                    ->icon('heroicon-o-phone')
                    ->getStateUsing(fn ($record) => $getAddress($record)['phone'] ?? '-')
                    ->url(fn ($state) => $state !== '-' ? 'tel:' . $state : false)
                    ->color(fn ($state) => $state !== '-' ? Color::Sky : null)
                    ->iconColor(fn ($state) => $state !== '-' ? Color::Green : null),
                Infolists\Components\TextEntry::make($type . '_email')
                    ->hiddenLabel()
                    ->icon('heroicon-o-envelope')
                    ->getStateUsing(fn ($record) => $getAddress($record)['email'] ?? '-')
                    ->url(fn ($state) => $state !== '-' ? 'mailto:' . $state : false)
                    ->color(fn ($state) => $state !== '-' ? Color::Sky : null)
                    ->iconColor(fn ($state) => $state !== '-' ? Color::Amber : null),
            ]);
    }
}
