<?php

namespace PictaStudio\VenditioAdmin\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PictaStudio\VenditioAdmin\Resources\BrandResource\Pages;
use PictaStudio\VenditioCore\Models\Contracts\Brand;

class BrandResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?int $navigationSort = 2;

    public static function getModel(): string
    {
        return app(Brand::class)::class;
    }

    public static function getModelLabel(): string
    {
        return __('venditio-admin::translations.brand.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('venditio-admin::translations.brand.label.plural');
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
                Section::make(__('venditio-admin::translations.brand.form.details.label'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('venditio-admin::translations.brand.form.name.label'))
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('venditio-admin::translations.brand.table.name.label')),
                TextColumn::make('products_count')
                    ->label(__('venditio-admin::translations.brand.table.products_count.label')),
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount([
                'products',
            ])
            ->withoutGlobalScopes([
                // SoftDeletingScope::class,
            ]);
    }
}
