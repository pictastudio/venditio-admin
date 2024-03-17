# This is my package venditio-admin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pictastudio/venditio-admin.svg?style=flat-square)](https://packagist.org/packages/pictastudio/venditio-admin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pictastudio/venditio-admin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pictastudio/venditio-admin/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/pictastudio/venditio-admin/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/pictastudio/venditio-admin/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pictastudio/venditio-admin.svg?style=flat-square)](https://packagist.org/packages/pictastudio/venditio-admin)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require pictastudio/venditio-admin
```

Then initialize filament with the following command

```bash
php artisan filament:install --panels
```

You can install the package with:

```bash
php artisan venditio-admin:install
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="venditio-admin-views"
```

Optionally, you can publish the translations using

```bash
php artisan vendor:publish --tag="venditio-admin-translations"
```

This is the contents of the published config file:

```php
return [

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
        'brand' => [
            'enabled' => true,
            'class' => Resources\BrandResource::class,
        ],
        'order' => [
            'enabled' => true,
            'class' => Resources\OrderResource::class,
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
];
```

## Auth
To manage auth and permissions this package uses `PictaStudio\VenditioCore\Managers\Contracts\AuthManager` from the [core](https://github.com/pictastudio/venditio-core) package.
In order to authoriza access to the filament panel the instance of `PictaStudio\VenditioCore\Managers\Contracts\AuthManager` that is binded into the container must implement the `canAccessAdminPanel` method

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [PictaStudio](https://github.com/pictastudio)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
