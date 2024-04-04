<?php

use Filament\Pages\Auth\EditProfile;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\Login;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Pages\Auth\Register;
use Filament\Widgets;
use PictaStudio\VenditioAdmin\Resources;
use PictaStudio\VenditioAdmin\Resources\ProductResource\RelationManagers\ProductItemsRelationManager;

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel
    |--------------------------------------------------------------------------
    |
    | Specify the admin panel general settings
    |
    */
    'panel' => [
        'path' => 'venditio-admin',
        'default' => true, // set the panel from this package as the default panel
        'spa' => true, // enable the single page application navigation mode
        'login' => Login::class,
        'profile' => [
            'enabled' => true,
            'class' => EditProfile::class,
            'simple' => true,
        ],
        'registration' => [
            'enabled' => true,
            'action' => Register::class,
        ],
        'email_verification' => [
            'enabled' => true,
            'prompt_action' => EmailVerificationPrompt::class,
        ],
        'password_reset' => [
            'enabled' => true,
            'request_action' => RequestPasswordReset::class,
            'reset_action' => ResetPassword::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Brand
    |--------------------------------------------------------------------------
    |
    | Specify the brand settings
    |
    */
    'brand' => [
        'name' => config('app.name'),
        'logo' => [
            'light' => null,
            'dark' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    |
    */
    'products' => [
        'variants' => [
            'enabled' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Clusters
    |--------------------------------------------------------------------------
    |
    | Specify clusters
    |
    */
    'clusters' => [
        'in' => __DIR__ . '/Clusters',
        'for' => 'PictaStudio\\VenditioAdmin\\Clusters',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | Specify the filament resources
    |
    */
    'resources' => [
        'default' => [
            'brand' => [
                'enabled' => true,
                'class' => Resources\BrandResource::class,
            ],
            'order' => [
                'enabled' => true,
                'class' => Resources\OrderResource::class,
                'configuration' => [
                    'order_lines' => [
                        'paginated' => true,
                    ],
                ],
            ],
            'product' => [
                'enabled' => true,
                'class' => Resources\ProductResource::class,
                'relation_managers' => [
                    'product_items' => [
                        'enabled' => true,
                        'class' => ProductItemsRelationManager::class,
                    ],
                ],
            ],
            'product_category' => [
                'enabled' => true,
                'class' => Resources\ProductCategoryResource::class,
            ],
            'user' => [
                'enabled' => true,
                'class' => Resources\UserResource::class,
            ],
        ],
        'extra' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    |
    | Specify additional widgets to add to the dashboard
    |
    */
    'widgets' => [
        'dashboard' => [
            'account' => [
                'enabled' => true,
                'class' => Widgets\AccountWidget::class,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Groups and Items
    |--------------------------------------------------------------------------
    |
    | Specify the navigation groups and items
    |
    */
    'navigation' => [
        'groups' => function () { // closure returning an array of Filament\Navigation\NavigationGroup instances
            return [
                null, // Filament\Navigation\NavigationGroup instance
                null, // Filament\Navigation\NavigationGroup instance
            ];
        },
        'items' => function () { // closure returning an array of Filament\Navigation\NavigationItem instances
            return [
                null, // Filament\Navigation\NavigationItem instance
                null, // Filament\Navigation\NavigationItem instance
            ];
        },
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Configuration
    |--------------------------------------------------------------------------
    |
    */
    'filament' => [
        'columns' => [
            'icon' => [
                'enable_click_to_toggle' => true,
            ],
        ],
    ],
];
