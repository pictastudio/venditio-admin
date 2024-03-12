<?php

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
        'dashboard' => [],
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
