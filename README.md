# Laravel Database State

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pxlrbt/laravel-db-state.svg?style=flat-square)](https://packagist.org/packages/pxlrbt/laravel-db-state)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pxlrbt/laravel-db-state/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pxlrbt/laravel-db-state/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/pxlrbt/laravel-db-state/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/pxlrbt/laravel-db-state/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pxlrbt/laravel-db-state.svg?style=flat-square)](https://packagist.org/packages/pxlrbt/laravel-db-state)

Seed critical state your databases with production data.

## Installation

You can install the package via composer:

```bash
composer require pxlrbt/laravel-db-state
```

### Add autoloader

Add the namespace to the `composer.json`

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/",
            "Database\\States\\": "database/states/"
        }
    }
}
```

### Create your first database state

You can create a new class via CLI: `php artisan make:db-state`. This will create an invokable class in `database/States` directory.

Make sure your database states are idempotent, so consecutive runs won't create duplicate entries or overwrite existing entries.

```php
<?php
namespace Database\States;

use App\Models\User;

class UserState
{
    public function __invoke()
    {
        if (! User::where('user', 'info@example.com')->exists()) {
            User::forceCreate([
                'name' => 'Example User',
                'email' => 'info@example.com',
                'email_verified_at' => now(),
                'password' => '$2y$10$etbrxzCyYhs598Abu6XdAeJ7GZQvDhOvE70XnRtoO25bvif1uEvSi',
            ]);
        }
    }
}
```

## Credits

- [Dennis Koch](https://github.com/pxlrbt)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
