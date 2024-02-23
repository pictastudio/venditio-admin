<?php

namespace PictaStudio\VenditioAdmin;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Table;
use Illuminate\Filesystem\Filesystem;
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
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations();
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
        Table::configureUsing(fn (Table $table) => (
            $table
                ->defaultPaginationPageOption(25)
                ->paginated([10, 25, 50, 100])
                ->selectCurrentPageOnly()
                ->deferLoading()
        ));

        Page::$reportValidationErrorUsing = fn (ValidationException $exception) => (
            Notification::make()
                ->title(__('filament-admin.notifications.validation.title'))
                ->body($exception->getMessage())
                ->persistent()
                ->danger()
                ->send()
        );
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
        return collect(scandir(__DIR__ . '/../database/migrations'))
            ->reject(fn (string $file) => in_array($file, ['.', '..']))
            ->map(fn (string $file) => str($file)->beforeLast('.php'))
            ->toArray();
    }
}
