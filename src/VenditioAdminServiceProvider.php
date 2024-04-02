<?php

namespace PictaStudio\VenditioAdmin;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\ExportAction as TableExportAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportTesting\Testable;
use PictaStudio\VenditioAdmin\Testing\TestsVenditioAdmin;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VenditioAdminServiceProvider extends PackageServiceProvider
{
    public static string $name = 'venditio-admin';

    public static string $viewNamespace = 'venditio-admin';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasRoute('web')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile();
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/venditio-admin/{$file->getFilename()}"),
                ], 'venditio-admin-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsVenditioAdmin());

        // Configurations
        FileUpload::configureUsing(fn (FileUpload $fileUpload) => (
            $fileUpload
                ->columnSpanFull()
                ->downloadable()
                ->hintAction(
                    Action::make('view')
                        ->label(__('venditio-admin::translations.global.forms.file_upload.hint_action.label'))
                        ->url(fn (array $state) => asset('storage/' . Arr::first($state)), shouldOpenInNewTab: true)
                )
        ));

        Repeater::configureUsing(fn (Repeater $repeater) => (
            $repeater
                ->columnSpanFull()
                ->defaultItems(0)
                ->collapsible()
                ->addAction(fn (Action $action) => (
                    $action->icon('heroicon-o-plus')
                ))
        ));

        ViewAction::configureUsing(fn (ViewAction $viewAction) => (
            $viewAction
                ->icon('heroicon-o-eye')
        ));

        CreateAction::configureUsing(fn (CreateAction $createAction) => (
            $createAction
                ->icon('heroicon-o-plus')
        ));

        DeleteAction::configureUsing(fn (DeleteAction $deleteAction) => (
            $deleteAction
                ->icon('heroicon-s-trash')
        ));

        ExportAction::configureUsing(fn (ExportAction $exportAction) => (
            $exportAction
                ->icon('heroicon-o-arrow-down-tray')
        ));

        TableExportAction::configureUsing(fn (TableExportAction $exportAction) => (
            $exportAction
                ->icon('heroicon-o-arrow-down-tray')
        ));

        Table::configureUsing(fn (Table $table) => (
            $table
                ->defaultPaginationPageOption(25)
                ->paginated([10, 25, 50, 100])
                ->selectCurrentPageOnly()
            // ->deferLoading()
        ));

        Page::$reportValidationErrorUsing = fn (ValidationException $exception) => (
            Notification::make()
                ->title(__('venditio-admin::translations.global.notifications.validation.title'))
                ->body($exception->getMessage())
                ->persistent()
                ->danger()
                ->send()
        );

        if (config('venditio-admin.filament.columns.icon.enable_click_to_toggle')) {
            IconColumn::configureUsing(function (IconColumn $iconColumn) {
                $iconColumn->action(function (Model $record, Column $column) {
                    $name = $column->getName();
                    $record->update([
                        $name => !$record->$name,
                    ]);

                    Notification::make()
                        ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                        ->success()
                        ->send();
                });

                $iconColumn->tooltip(__('venditio-admin::translations.global.columns.icon.toggle.tooltip'));

                $iconColumn->alignCenter();

                return $iconColumn;
            });
        }
    }

    protected function getAssetPackageName(): ?string
    {
        return 'pictastudio/venditio-admin';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('venditio-admin', __DIR__ . '/../resources/dist/components/venditio-admin.js'),
            // Css::make('venditio-admin-styles', __DIR__ . '/../resources/dist/venditio-admin.css'),
            // Js::make('venditio-admin-scripts', __DIR__ . '/../resources/dist/venditio-admin.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [

        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [];
    }
}
