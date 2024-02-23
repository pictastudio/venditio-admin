<?php

namespace PictaStudio\VenditioAdmin\Resources\ProductResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use PictaStudio\VenditioCore\Models\Product;
use PictaStudio\VenditioCore\Models\ProductItem;

class ProductItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'productItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema(static::getSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([

            ])
            ->headerActions([
                // CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getSchema(): array
    {
        return [
            Tabs::make()
                ->columnSpanFull()
                ->schema([
                    Tab::make(__('venditio-admin::product.form.tabs.details.label'))
                        ->icon('heroicon-o-information-circle')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label(__('venditio-admin::product.form.name.label'))
                                ->required()
                                ->maxLength(255),
                            Select::make('status')
                                ->label(__('venditio-admin::product.form.status.label'))
                                ->required()
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                ]),
                            TextInput::make('description_short')
                                ->label(__('venditio-admin::product.form.description_short.label'))
                                ->required()
                                ->maxLength(255),
                            TextInput::make('description')
                                ->label(__('venditio-admin::product.form.description.label'))
                                ->required()
                                ->maxLength(255),
                            Section::make(__('venditio-admin::product.form.visibility.label'))
                                ->columns(2)
                                ->collapsible()
                                ->schema([
                                    Toggle::make('active')
                                        ->label(__('venditio-admin::product.form.active.label'))
                                        ->default(true)
                                        ->columnSpanFull(),
                                    // Toggle::make('new')
                                    //     ->label(__('venditio-admin::product.form.new.label'))
                                    //     ->default(true)
                                    //     ->columnSpanFull(),
                                    // Toggle::make('in_evidence')
                                    //     ->label(__('venditio-admin::product.form.in_evidence.label'))
                                    //     ->default(true)
                                    //     ->columnSpanFull(),
                                    DateTimePicker::make('visible_from')
                                        ->label(__('venditio-admin::product.form.visible_from.label'))
                                        ->nullable(),
                                    DateTimePicker::make('visible_to')
                                        ->label(__('venditio-admin::product.form.visible_to.label'))
                                        ->after('visible_from')
                                        ->nullable(),
                                ]),
                        ]),
                    Tab::make(__('venditio-admin::product.form.tabs.dimensions.label'))
                        ->icon('heroicon-o-cube')
                        ->columns(2)
                        ->schema([
                            TextInput::make('weight')
                                ->label(__('venditio-admin::product.form.weight.label'))
                                ->nullable()
                                ->numeric()
                                ->formatStateUsing(fn (?ProductItem $record) => $record?->weight?->decimal()),
                            TextInput::make('length')
                                ->label(__('venditio-admin::product.form.length.label'))
                                ->nullable()
                                ->numeric()
                                ->formatStateUsing(fn (?ProductItem $record) => $record?->length?->decimal()),
                            TextInput::make('width')
                                ->label(__('venditio-admin::product.form.width.label'))
                                ->nullable()
                                ->numeric()
                                ->formatStateUsing(fn (?ProductItem $record) => $record?->width?->decimal()),
                            TextInput::make('depth')
                                ->label(__('venditio-admin::product.form.depth.label'))
                                ->nullable()
                                ->numeric()
                                ->formatStateUsing(fn (?ProductItem $record) => $record?->depth?->decimal()),
                        ]),
                    Tab::make(__('venditio-admin::product.form.tabs.images.label'))
                        ->icon('heroicon-o-photo')
                        ->visibleOn('edit')
                        ->schema([
                            Repeater::make('images')
                                ->label(false)
                                ->addActionLabel(__('venditio-admin::product.form.images.add_button_label'))
                                ->defaultItems(0)
                                ->grid(2)
                                ->schema([
                                    FileUpload::make('img')
                                        ->label(__('venditio-admin::product.form.images.img.label'))
                                        ->required()
                                        ->image()
                                        ->directory(fn (ProductItem $record) => "products/{$record->product_id}/product_items/{$record->getKey()}"),
                                    TextInput::make('alt')
                                        ->label(__('venditio-admin::product.form.images.alt.label'))
                                        ->required()
                                        ->maxLength(255),
                                ]),
                        ]),
                    Tab::make(__('venditio-admin::product.form.tabs.metadata.label'))
                        ->icon('heroicon-o-cog')
                        ->schema([
                            KeyValue::make('metadata')
                                ->label(false)
                                ->addActionLabel(__('venditio-admin::product.form.metadata.add_button_label'))
                                ->schema([
                                    TextInput::make('key')
                                        ->label(__('venditio-admin::product.form.metadata.key.label'))
                                        ->nullable()
                                        ->maxLength(255),
                                    TextInput::make('value')
                                        ->label(__('venditio-admin::product.form.metadata.value.label'))
                                        ->nullable()
                                        ->maxLength(255),
                                ]),
                        ]),
                ]),
        ];
    }
}
