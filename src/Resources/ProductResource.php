<?php

namespace PictaStudio\VenditioAdmin\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use PictaStudio\VenditioAdmin\Resources\ProductResource\Pages;
use PictaStudio\VenditioCore\Models\Product;
use PictaStudio\VenditioCore\Models\Scopes\Active;
use PictaStudio\VenditioCore\Models\Scopes\InDateRange;

class ProductResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getModel(): string
    {
        return config('venditio-core.models.product');
    }

    public static function getModelLabel(): string
    {
        return __('venditio-admin::translations.product.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('venditio-admin::translations.product.label.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('venditio-admin::translations.global.sections.catalog');
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return 'name';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->schema([
                        Tab::make(__('venditio-admin::translations.product.form.tabs.details'))
                            ->icon('heroicon-o-information-circle')
                            ->columns(2)
                            ->schema([
                                Fieldset::make(__('venditio-admin::translations.product.form.connections.label'))
                                    ->columns(2)
                                    ->schema([
                                        Select::make('brand_id')
                                            ->label(__('venditio-admin::translations.product.form.brand.label'))
                                            ->required()
                                            ->relationship('brand', 'name')
                                            ->searchable(),
                                        Select::make('product_type_id')
                                            ->label(__('venditio-admin::translations.product.form.product_type.label'))
                                            ->required()
                                            ->relationship('productType', 'name'),
                                        Select::make('tax_class_id')
                                            ->label(__('venditio-admin::translations.product.form.tax_class.label'))
                                            ->required()
                                            ->relationship('taxClass', 'name'),
                                        Select::make('categories')
                                            ->label(__('venditio-admin::translations.product.form.categories.label'))
                                            ->required()
                                            ->relationship('categories', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                TextInput::make('name')
                                    ->label(__('venditio-admin::translations.product.form.name.label'))
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->label(__('venditio-admin::translations.product.form.status.label'))
                                    ->required()
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                    ]),
                                TextInput::make('description_short')
                                    ->label(__('venditio-admin::translations.product.form.description_short.label'))
                                    ->columnSpanFull()
                                    ->maxLength(255),
                                RichEditor::make('description')
                                    ->label(__('venditio-admin::translations.product.form.description.label'))
                                    ->columnSpanFull()
                                    ->maxLength(65535),
                                Section::make(__('venditio-admin::translations.product.form.visibility.label'))
                                    ->columns(2)
                                    ->collapsible()
                                    ->schema([
                                        Toggle::make('active')
                                            ->label(__('venditio-admin::translations.product.form.active.label'))
                                            ->default(true)
                                            ->columnSpanFull(),
                                        Toggle::make('new')
                                            ->label(__('venditio-admin::translations.product.form.new.label'))
                                            ->default(true)
                                            ->columnSpanFull(),
                                        Toggle::make('in_evidence')
                                            ->label(__('venditio-admin::translations.product.form.in_evidence.label'))
                                            ->default(true)
                                            ->columnSpanFull(),
                                        DateTimePicker::make('visible_from')
                                            ->label(__('venditio-admin::translations.product.form.visible_from.label'))
                                            ->nullable(),
                                        DateTimePicker::make('visible_to')
                                            ->label(__('venditio-admin::translations.product.form.visible_to.label'))
                                            ->after('visible_from')
                                            ->nullable(),
                                    ]),
                            ]),
                        Tab::make(__('venditio-admin::translations.product.form.tabs.dimensions'))
                            ->icon('heroicon-o-cube')
                            ->columns(5)
                            ->schema([
                                Select::make('measuring_unit')
                                    ->label(__('venditio-admin::translations.product.form.measuring_unit.label'))
                                    ->nullable()
                                    ->columnSpanFull()
                                    ->options([
                                        'pz' => 'pz',
                                    ]),
                                TextInput::make('weight')
                                    ->label(__('venditio-admin::translations.product.form.weight.label'))
                                    ->nullable()
                                    ->numeric(),
                                TextInput::make('length')
                                    ->label(__('venditio-admin::translations.product.form.length.label'))
                                    ->nullable()
                                    ->numeric(),
                                TextInput::make('width')
                                    ->label(__('venditio-admin::translations.product.form.width.label'))
                                    ->nullable()
                                    ->numeric(),
                                TextInput::make('height')
                                    ->label(__('venditio-admin::translations.product.form.height.label'))
                                    ->nullable()
                                    ->numeric(),
                                TextInput::make('depth')
                                    ->label(__('venditio-admin::translations.product.form.depth.label'))
                                    ->nullable()
                                    ->numeric(),
                            ]),
                        Tab::make(__('venditio-admin::translations.product.form.tabs.images'))
                            ->icon('heroicon-o-photo')
                            ->visibleOn('edit')
                            ->schema([
                                Repeater::make('images')
                                    ->label(false)
                                    ->addActionLabel(__('venditio-admin::translations.product.form.images.add_button_label'))
                                    ->defaultItems(0)
                                    ->grid(2)
                                    ->schema([
                                        FileUpload::make('img')
                                            ->label(__('venditio-admin::translations.product.form.images.img.label'))
                                            ->required()
                                            ->image()
                                            ->directory(fn (Product $record) => "products/{$record->getKey()}"),
                                        TextInput::make('alt')
                                            ->label(__('venditio-admin::translations.product.form.images.alt.label'))
                                            ->helperText(__('venditio-admin::translations.product.form.images.alt.helper_text'))
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ]),
                        Tab::make(__('venditio-admin::translations.product.form.tabs.metadata'))
                            ->icon('heroicon-o-cog')
                            ->schema([
                                KeyValue::make('metadata')
                                    ->label(false)
                                    ->addActionLabel(__('venditio-admin::translations.product.form.metadata.add_button_label'))
                                    ->schema([
                                        TextInput::make('key')
                                            ->label(__('venditio-admin::translations.product.form.metadata.key.label'))
                                            ->nullable()
                                            ->maxLength(255),
                                        TextInput::make('value')
                                            ->label(__('venditio-admin::translations.product.form.metadata.value.label'))
                                            ->nullable()
                                            ->maxLength(255),
                                    ]),
                            ]),
                        Tab::make(__('venditio-admin::translations.product.form.tabs.variants'))
                            ->visible(fn (Component $livewire) => (
                                $livewire instanceof Pages\EditProduct && config('venditio-admin.products.variants.enabled')
                            ))
                            ->dehydrated(false)
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Repeater::make('variants')
                                    ->label(false)
                                    ->dehydrated(false)
                                    ->addActionLabel(__('venditio-admin::translations.product.form.variants.add_button_label'))
                                    ->defaultItems(0)
                                    ->grid(2)
                                    ->schema([

                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('venditio-admin::translations.product.table.name.label'))
                    ->searchable(),
                IconColumn::make('active')
                    ->label(__('venditio-admin::translations.product.table.active.label'))
                    ->icon(fn (Product $record) => $record->active ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
            ])
            ->filters([

            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return collect(config('venditio-admin.resources.product.relation_managers'))
            ->filter(fn (array $manager) => $manager['enabled'])
            ->map(fn (array $manager) => $manager['class'])
            ->toArray();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                Active::class,
                InDateRange::class,
                // SoftDeletingScope::class,
            ]);
    }
}
