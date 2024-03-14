<?php

namespace PictaStudio\VenditioAdmin;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Collection;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use PictaStudio\VenditioAdmin\Pages\Auth\Login;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // $brandAsset = function ($asset) {
        //     $vendorPath = 'vendor/lunarpanel/';

        //     if (file_exists(public_path($vendorPath.$asset))) {
        //         return asset($vendorPath.$asset);
        //     } else {
        //         $type = str($asset)
        //             ->endsWith('.png') ? 'image/png' : 'image/svg+xml';

        //         return "data:{$type};base64,".base64_encode(file_get_contents(__DIR__.'/../public/'.$asset));
        //     }
        // };

        // return $panel;

        return $panel
            ->spa(config('venditio-admin.panel.spa', false))
            ->default(config('venditio-admin.panel.default'))
            ->id('venditio-admin')
            ->login()
            // ->passwordReset()
            // ->path('admin')
            ->path(config('venditio-admin.panel.path'))
            // ->login(Login::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->brandName(config('venditio-admin.brand.name'))
            ->brandLogo(config('venditio-admin.brand.logo.light'))
            ->darkModeBrandLogo(config('venditio-admin.brand.logo.dark'))
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Gray,
                'orange' => Color::Orange,
                'emerald' => Color::Emerald,
                'sky' => Color::Sky,
                'purple' => Color::Purple,
                'pink' => Color::Pink,
            ])
            ->discoverClusters(config('venditio-admin.clusters.in'), config('venditio-admin.clusters.for'))
            // ->discoverResources(in: __DIR__ . '/Resources', for: 'PictaStudio\\VenditioAdmin\\Resources')
            ->resources(
                $this->getResources('venditio-admin.resources.default')
                    ->merge(
                        $this->getResources('venditio-admin.resources.extra')
                    )
                    ->toArray()
            )
            ->discoverPages(in: __DIR__ . '/Pages', for: 'PictaStudio\\VenditioAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: __DIR__ . '/Widgets', for: 'PictaStudio\\VenditioAdmin\\Widgets')
            ->widgets(
                $this->getWidgets()->toArray()
            )
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::Full)
            // ->navigationGroups([
            //     NavigationGroup::make()
            //         ->label('Users & Roles')
            //         // ->icon('heroicon-m-cog-6-tooth')
            //         ->collapsed(),
            // ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn () => __('venditio-admin::translations.global.widgets.dashboard.brand.visit_site'))
                    // ->url(route('filament.admin.resources.users.index'))
                    ->url('/', true)
                    ->icon('heroicon-m-globe-alt'),
            ])
            // ->registration()
            // ->passwordReset()
            // ->emailVerification()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
        // ->loginRouteSlug('/filament/login')
        // ->registrationRouteSlug('/filament/register')
        // ->passwordResetRoutePrefix('/filament/password-reset')
        // ->passwordResetRequestRouteSlug('/filament/request')
        // ->passwordResetRouteSlug('/filament/reset')
        // ->emailVerificationRoutePrefix('/filament/email-verification')
        // ->emailVerificationPromptRouteSlug('/filament/prompt')
        // ->emailVerificationRouteSlug('/filament/verify');
    }

    public function getResources(string $key): Collection
    {
        return collect(config($key))
            ->filter(fn (array $resource) => $resource['enabled'])
            ->map(fn (array $resource) => $resource['class'])
            ->values();
    }

    public function getWidgets(): Collection
    {
        return collect(config('venditio-admin.widgets.dashboard'))
            ->filter(fn (array $resource) => $resource['enabled'])
            ->map(fn (array $resource) => $resource['class'])
            ->values();
    }
}
