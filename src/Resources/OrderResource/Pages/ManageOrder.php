<?php

namespace PictaStudio\VenditioAdmin\Resources\OrderResource\Pages;

use Awcodes\Shout\Components\ShoutEntry;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Lunar\DataTypes\Price;
use Lunar\Models\State;
use PictaStudio\VenditioAdmin\Enums\OrderStatus;
use PictaStudio\VenditioAdmin\Resources\OrderResource\Pages\Components\OrderItemsTable;
use PictaStudio\VenditioAdmin\Resources\UserResource;
use PictaStudio\VenditioAdmin\Support\Infolists\Components\Livewire;
use PictaStudio\VenditioAdmin\Support\Infolists\Components\Tags;
use PictaStudio\VenditioAdmin\Support\Infolists\Components\Timeline;
use PictaStudio\VenditioAdmin\Support\Infolists\Components\Transaction as InfolistsTransaction;
use PictaStudio\VenditioCore\Models\Order;

class ManageOrder extends ViewRecord
{
    protected static string $view = 'venditio-admin::resources.order-resource.pages.manage-order';

    public static function getResource(): string
    {
        return config('venditio-admin.resources.default.order.class');
    }

    public function getBreadcrumb(): string
    {
        return __('venditio-admin::translations.order.breadcrumb.manage');
    }

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
            ActionsAction::make('approved_at')
                ->label(__('venditio-admin::translations.order.infolist.approved_at.action.label'))
                ->color('emerald')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => $this->getRecord()->approved_at === null)
                ->modalSubmitActionLabel(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action(fn (Order $record) => (
                    $record->update([
                        'status' => OrderStatus::COMPLETED,
                        'approved_at' => now(),
                    ])
                ))
                ->after(function () {
                    Notification::make()
                        ->label(__('venditio-admin::translations.order.infolist.approved_at.notification.title'))
                        ->success()
                        ->send();
                }),
            ActionsAction::make('admin_notes')
                ->label(__('venditio-admin::translations.order.infolist.admin_notes.label'))
                ->color('sky')
                ->icon('heroicon-o-pencil-square')
                ->modalSubmitActionLabel(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->form([
                    Textarea::make('admin_notes')
                        ->label(false)
                        ->placeholder(__('venditio-admin::translations.order.infolist.admin_notes.placeholder'))
                        ->afterStateHydrated(function (Set $set) {
                            $set('admin_notes', $this->record->admin_notes);
                        })
                        ->rows(3)
                        ->required(),
                ])
                ->action(fn (Order $record, array $data) => (
                    $record->update(['admin_notes' => $data['admin_notes']])
                ))
                ->after(function () {
                    Notification::make()
                        ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('venditio-admin::translations.order.label.singular') . ' #' . $this->record->identifier;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->schema([
                Group::make()
                    ->columnSpan(['lg' => 2])
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

                        Section::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        // TextEntry::make('shipping_fee_description')
                                        //     ->icon('heroicon-s-truck')
                                        //     ->html()
                                        //     ->iconPosition(IconPosition::Before)
                                        //     ->hiddenLabel(),
                                        TextEntry::make('shipping_fee')
                                            ->hiddenLabel()
                                            ->alignEnd(),
                                    ]),
                                Grid::make()
                                    ->columns(2)
                                    ->schema([
                                        Grid::make()
                                            ->columns(1)
                                            ->columnSpan(1)
                                            ->schema([
                                                // TextEntry::make('shippingAddress.delivery_instructions')
                                                //     ->label(__('lunarpanel::order.infolist.delivery_instructions.label'))
                                                //     ->hidden(fn ($state) => blank($state)),
                                                TextEntry::make('addresses.shipping.notes')
                                                    ->label(__('venditio-admin::translations.order.infolist.notes.label')),
                                                TextEntry::make('admin_notes')
                                                    ->label(__('venditio-admin::translations.order.infolist.admin_notes.label')),
                                                // ->placeholder(__('venditio-admin::translations.order.infolist.notes.placeholder')),
                                            ]),
                                        Grid::make()
                                            ->columns(1)
                                            ->columnSpan(1)
                                            ->schema([
                                                TextEntry::make('sub_total')
                                                    ->label(__('venditio-admin::translations.order.infolist.sub_total.label'))
                                                    ->inlineLabel()
                                                    ->alignEnd(),
                                                TextEntry::make('discount_amount')
                                                    ->label(__('venditio-admin::translations.order.infolist.discount_amount.label'))
                                                    ->inlineLabel()
                                                    ->alignEnd(),
                                                TextEntry::make('shipping_fee')
                                                    ->label(__('venditio-admin::translations.order.infolist.shipping_fee.label'))
                                                    ->inlineLabel()
                                                    ->alignEnd(),
                                                TextEntry::make('payment_fee')
                                                    ->label(__('venditio-admin::translations.order.infolist.payment_fee.label'))
                                                    ->inlineLabel()
                                                    ->alignEnd(),

                                                // Group::make()
                                                //     ->statePath('tax_breakdown')
                                                //     ->schema(function ($state) {
                                                //         $taxes = [];
                                                //         foreach ($state->amounts ?? [] as $taxIndex => $tax) {
                                                //             $taxes[] = TextEntry::make('tax_'.$taxIndex)
                                                //                 ->label(fn () => $tax->description)
                                                //                 ->inlineLabel()
                                                //                 ->alignEnd()
                                                //                 ->state(fn () => $tax->price->formatted);
                                                //         }

                                                //         return $taxes;
                                                //     }),
                                                TextEntry::make('total_final')
                                                    ->label(fn () => new HtmlString('<b>' . __('venditio-admin::translations.order.infolist.total_final.label') . '</b>'))
                                                    ->inlineLabel()
                                                    ->alignEnd()
                                                    ->weight(FontWeight::Bold),
                                                // TextEntry::make('paid')
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
                                                // TextEntry::make('refund')
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

                        // Section::make('transactions')
                        //     ->heading(__('lunarpanel::order.infolist.transactions.label'))
                        //     ->compact()
                        //     ->collapsed(fn ($state) => filled($state))
                        //     ->collapsible(fn ($state) => filled($state))
                        //     ->schema([
                        //         RepeatableEntry::make('transactions')
                        //             ->hiddenLabel()
                        //             ->placeholder(__('lunarpanel::order.infolist.transactions.placeholder'))
                        //             ->getStateUsing(fn ($record) => $record->transactions->sortByDesc('created_at')->sortBy('id'))
                        //             ->contained(false)
                        //             ->schema([
                        //                 InfolistsTransaction::make('transactions'),
                        //             ]),
                        //     ]),

                        // Grid::make()
                        //     ->schema([
                        //         Timeline::make('timeline')
                        //             ->label(__('lunarpanel::order.infolist.timeline.label')),
                        //     ]),

                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        TextEntry::make('user')
                            ->hidden(fn ($state) => blank($state?->id))
                            ->formatStateUsing(fn ($state) => $state->name)
                            ->weight(FontWeight::SemiBold)
                            ->size(TextEntrySize::Large)
                            ->hiddenLabel()
                            ->suffixAction(
                                fn ($state) => Action::make('view customer')
                                    ->label(__('venditio-admin::translations.order.infolist.view_customer.label'))
                                    ->color('gray')
                                    ->button()
                                    ->size(ActionSize::ExtraSmall)
                                    ->openUrlInNewTab()
                                    ->url(function () {
                                        if (!collect(Filament::getResources())->contains(config('venditio-admin.resources.default.user.class'))) {
                                            return;
                                        }

                                        return UserResource::getUrl('edit', ['record' => $this->getRecord()->user->getKey()]);
                                    })
                            ),
                        Section::make()
                            ->compact()
                            ->inlineLabel()
                            ->schema([
                                TextEntry::make('status')
                                    ->label(__('venditio-admin::translations.order.infolist.status.label'))
                                    ->alignEnd()
                                    ->badge(),
                                TextEntry::make('identifier')
                                    ->label(__('venditio-admin::translations.order.infolist.identifier.label'))
                                    ->alignEnd()
                                    ->icon('heroicon-o-clipboard')
                                    ->iconPosition(IconPosition::After)
                                    ->copyable(),
                                TextEntry::make('created_at')
                                    ->label(__('venditio-admin::translations.order.infolist.date_created.label'))
                                    ->alignEnd()
                                    ->dateTime('Y-m-d H:i')
                                    ->visible(fn (Order $record) => !$record->approved_at),
                                TextEntry::make('approved_at')
                                    ->label(__('venditio-admin::translations.order.infolist.date_approved.label'))
                                    ->alignEnd()
                                    ->dateTime('Y-m-d H:i')
                                    ->placeholder('-'),
                            ]),
                        Section::make(__('venditio-admin::translations.order.infolist.user_info.label'))
                            ->compact()
                            ->inlineLabel()
                            ->schema([
                                TextEntry::make('user_first_name')
                                    ->label(__('venditio-admin::translations.order.infolist.user_first_name.label'))
                                    ->alignEnd(),
                                TextEntry::make('user_last_name')
                                    ->label(__('venditio-admin::translations.order.infolist.user_last_name.label'))
                                    ->alignEnd(),
                                TextEntry::make('user_email')
                                    ->label(__('venditio-admin::translations.order.infolist.user_email.label'))
                                    ->alignEnd(),
                            ]),
                        $this->getOrderAddressInfolistSchema('shipping'),
                        $this->getOrderAddressInfolistSchema('billing'),
                        // Section::make('tags')
                        //     ->heading(__('lunarpanel::order.infolist.tags.label'))
                        //     ->headerActions([
                        //         fn ($record) => $this->getEditTagsActions(),
                        //     ])
                        //     ->compact()
                        //     ->schema([
                        //         Tags::make(''),
                        //     ]),

                        // Section::make('additional_info')
                        //     ->heading(__('lunarpanel::order.infolist.additional_info.label'))
                        //     ->compact()
                        //     ->statePath('meta')
                        //     ->schema(fn ($state) => blank($state) ? [
                        //         TextEntry::make('no_additional_info')
                        //             ->hiddenLabel()
                        //             // ->weight(FontWeight::SemiBold)
                        //             ->getStateUsing(fn () => __('lunarpanel::order.infolist.no_additional_info.label')),
                        //     ] : collect($state)
                        //         ->map(fn ($value, $key) => TextEntry::make('meta_'.$key)
                        //             ->state($value)
                        //             ->label($key)
                        //             ->inlineLabel())
                        //         ->toArray()),

                    ]),
            ]);
    }

    public function getOrderAddressInfolistSchema(string $type)
    {
        // $sameAsShipping = fn ($record) => $type == 'billing' && $this->isShippingEqualsBilling($record->shippingAddress, $record->billingAddress);

        $getAddress = fn ($record) => match ($type) {
            'billing' => $record->addresses['billing'],
            'shipping' => $record->addresses['shipping'],
            default => null,
        };

        return Section::make(__("venditio-admin::translations.order.infolist.{$type}_address.label"))
            // ->statePath($type.'Address')
            ->compact()
            // ->headerActions([
            //     fn ($record) => $this->getEditAddressAction($type)->hidden($sameAsShipping($record)),
            // ])
            ->schema([
                TextEntry::make($type . '_address')
                    ->hiddenLabel()
                    ->listWithLineBreaks()
                    ->getStateUsing(function ($record) use ($getAddress) {
                        $address = $getAddress($record);

                        if (!empty($address)) {
                            return collect([
                                __('venditio-admin::translations.order.infolist.address.first_name') => $address['first_name'],
                                __('venditio-admin::translations.order.infolist.address.last_name') => $address['last_name'],
                                __('venditio-admin::translations.order.infolist.address.email') => $address['email'],
                                __('venditio-admin::translations.order.infolist.address.sex') => $address['sex'],
                                __('venditio-admin::translations.order.infolist.address.phone') => $address['phone'],
                                __('venditio-admin::translations.order.infolist.address.vat_number') => $address['vat_number'],
                                __('venditio-admin::translations.order.infolist.address.fiscal_code') => $address['fiscal_code'],
                                __('venditio-admin::translations.order.infolist.address.company_name') => $address['company_name'],
                                __('venditio-admin::translations.order.infolist.address.address_line_1') => $address['address_line_1'],
                                __('venditio-admin::translations.order.infolist.address.address_line_2') => $address['address_line_2'],
                                __('venditio-admin::translations.order.infolist.address.city') => $address['city'],
                                __('venditio-admin::translations.order.infolist.address.state') => $address['state'],
                                __('venditio-admin::translations.order.infolist.address.zip') => $address['zip'],
                            ])
                                ->mapWithKeys(function ($value, $key) {
                                    return [
                                        $key => "{$key}: {$value}",
                                        // $key => (new HtmlString("<b>{$key}:</b> {$value}"))->toHtml()
                                    ];
                                })
                                ->toArray();
                        }

                        return __('venditio-admin::translations.order.infolist.address_not_set.label');
                    }),
                TextEntry::make($type . '_phone')
                    ->hiddenLabel()
                    ->icon('heroicon-o-phone')
                    ->getStateUsing(fn ($record) => $getAddress($record)['phone'] ?? '-')
                    ->url(fn ($state) => $state !== '-' ? 'tel:' . $state : false)
                    ->color(fn ($state) => $state !== '-' ? Color::Sky : null)
                    ->iconColor(fn ($state) => $state !== '-' ? Color::Green : null),
                TextEntry::make($type . '_email')
                    ->hiddenLabel()
                    ->icon('heroicon-o-envelope')
                    ->getStateUsing(fn ($record) => $getAddress($record)['email'] ?? '-')
                    ->url(fn ($state) => $state !== '-' ? 'mailto:' . $state : false)
                    ->color(fn ($state) => $state !== '-' ? Color::Sky : null)
                    ->iconColor(fn ($state) => $state !== '-' ? Color::Amber : null),
            ]);
    }
}
