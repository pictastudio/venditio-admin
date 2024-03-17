<?php

namespace PictaStudio\VenditioAdmin\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PictaStudio\VenditioAdmin\Resources\ProductCategoryResource\Pages;
use PictaStudio\VenditioCore\Models\Contracts\ProductCategory;

class ProductCategoryResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function getModel(): string
    {
        return app(ProductCategory::class)::class;
    }

    public static function getModelLabel(): string
    {
        return __('venditio-admin::translations.product_category.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('venditio-admin::translations.product_category.label.plural');
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
                Section::make(__('venditio-admin::translations.product_category.form.details.label'))
                    ->columns(3)
                    ->schema([
                        Select::make('parent_id')
                            ->label(__('venditio-admin::translations.product_category.form.parent_id.label'))
                            ->relationship('parent', 'name')
                            ->placeholder(__('venditio-admin::translations.product_category.form.parent_id.placeholder'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        TextInput::make('name')
                            ->label(__('venditio-admin::translations.product_category.form.name.label'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('order')
                            ->label(__('venditio-admin::translations.product_category.form.order.label'))
                            ->required()
                            ->maxLength(255),
                        Toggle::make('active')
                            ->label(__('venditio-admin::translations.product_category.form.active.label'))
                            ->columnSpanFull()
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parent.name')
                    ->label(__('venditio-admin::translations.product_category.table.parent_id.label'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('venditio-admin::translations.product_category.table.name.label'))
                    ->searchable(),
                TextColumn::make('order')
                    ->label(__('venditio-admin::translations.product_category.table.order.label')),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'parent',
            ])
            ->withoutGlobalScopes([
                // SoftDeletingScope::class,
            ]);
    }
}
