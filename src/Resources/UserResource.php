<?php

namespace PictaStudio\VenditioAdmin\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use PictaStudio\VenditioAdmin\Resources\UserResource\Pages;
use PictaStudio\VenditioCore\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): ?string
    {
        return __('venditio-admin::translations.global.sections.users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('venditio-admin::translations.user.form.registry.label'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('venditio-admin::translations.user.form.first_name.label'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label(__('venditio-admin::translations.user.form.last_name.label'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('venditio-admin::translations.user.form.email.label'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        // TextInput::make('password')
                        //     ->label(__('venditio-admin::translations.user.form.password.label'))
                        //     ->password()
                        //     ->revealable()
                        //     ->required()
                        //     ->maxLength(255),
                        Toggle::make('active')
                            ->label(__('venditio-admin::translations.user.form.active.label'))
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([

            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
