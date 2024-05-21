<?php

namespace PictaStudio\VenditioAdmin;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Pages\Auth\EditProfile;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\Login;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Pages\Auth\Register;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $brandLogo = function (?string $path = null) {
            if ($path && file_exists(public_path($path))) {
                return asset($path);
            }

            return null;
        };

        $colors = [
            'primary' => Color::Amber,
            'gray' => Color::Gray,
            'orange' => Color::Orange,
            'emerald' => Color::Emerald,
            'sky' => Color::Sky,
            'purple' => Color::Purple,
            'pink' => Color::Pink,
        ];

        $configColors = config('venditio-admin.panel.colors');
        foreach ($configColors as $key => $value) {
            $colors[$key] = $value;
        }

        return $panel
            ->spa(config('venditio-admin.panel.spa', false))
            ->default(config('venditio-admin.panel.default'))
            ->id('venditio-admin')
            ->login(config('venditio-admin.panel.login', Login::class))
            ->when(
                config('venditio-admin.panel.email_verification.enabled', false),
                fn (Panel $panel) => $panel->emailVerification(
                    config('venditio-admin.panel.email_verification.prompt_action', EmailVerificationPrompt::class)
                )
            )
            ->when(
                config('venditio-admin.panel.registration.enabled', false),
                fn (Panel $panel) => $panel->registration(
                    config('venditio-admin.panel.registration.action', Register::class)
                )
            )
            ->when(
                config('venditio-admin.panel.profile.enabled', false),
                fn (Panel $panel) => $panel->profile(
                    config('venditio-admin.panel.profile.class', EditProfile::class),
                    config('venditio-admin.panel.profile.simple', true)
                )
            )
            ->when(
                config('venditio-admin.panel.password_reset.enabled', false),
                fn (Panel $panel) => $panel->passwordReset(
                    config('venditio-admin.panel.password_reset.request_action', RequestPasswordReset::class),
                    config('venditio-admin.panel.password_reset.reset_action', ResetPassword::class)
                )
            )
            // ->loginRouteSlug('/filament/login')
            // ->registrationRouteSlug('/filament/register')
            // ->passwordResetRoutePrefix('/filament/password-reset')
            // ->passwordResetRequestRouteSlug('/filament/request')
            // ->passwordResetRouteSlug('/filament/reset')
            // ->emailVerificationRoutePrefix('/filament/email-verification')
            // ->emailVerificationPromptRouteSlug('/filament/prompt')
            // ->emailVerificationRouteSlug('/filament/verify')
            ->path(config('venditio-admin.panel.path'))
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->brandName(config('venditio-admin.brand.name'))
            ->brandLogo($brandLogo(config('venditio-admin.brand.logo.light')))
            ->darkModeBrandLogo($brandLogo(config('venditio-admin.brand.logo.dark')))
            ->colors($colors)
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
            ->navigationGroups($this->getNavigationGroups())
            ->navigationItems($this->getNavigationItems())
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
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->middleware(
                $this->getMiddlewares()->toArray()
            )
            ->authMiddleware([
                Authenticate::class,
            ]);
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

    public function getMiddlewares(): Collection
    {
        $default = [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];

        return collect($default)
            ->prepend(...config('venditio-admin.panel.middleware.prepend'))
            ->push(...config('venditio-admin.panel.middleware.append'))
            ->values();
    }

    public function getNavigationGroups(): array
    {
        return config('venditio-admin.navigation.groups')();
    }

    public function getNavigationItems(): array
    {
        return config('venditio-admin.navigation.items')();
    }
}
