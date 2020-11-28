# Integrate Kanuu to your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kanuu-io/kanuu-laravel.svg)](https://packagist.org/packages/kanuu-io/kanuu-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/kanuu-io/kanuu-laravel/Tests?label=tests)](https://github.com/kanuu-io/kanuu-laravel/actions?query=workflow%3ATests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kanuu-io/kanuu-laravel.svg)](https://packagist.org/packages/kanuu-io/kanuu-laravel)

🛶 Quickly integrate your Laravel application with Kanuu in **4 simple steps**.

## Installation

##### 1. Require package.
```bash
composer require kanuu-io/kanuu-laravel
```

##### 2. Publish assets.

```bash
php artisan vendor:publish --tag=kanuu-config
php artisan vendor:publish --tag=kanuu-migrations
php artisan vendor:publish --tag=kanuu-provider
```

##### 3. Migrate.

```bash
php artisan migrate
```

## Getting started

##### 1. Add your Kanuu API key to your `.env` file.

```php
KANUU_API_KEY="YOUR_API_KEY_HERE"
```

##### 2. Register the Service-Provider in your config/app.php on the
`providers` array.
```php
...
App\Providers\KanuuServiceProvider::class,
...
```

##### 3. In your `app/Http/Middleware/VerifyCsrfToken.php` add the following:

```php
protected $except = [
    'webhooks/*',
];
```

##### 4. Now, all you need to do is add a "Manage your subscription" button that uses that route.

```html
<a href="{{ route('kanuu.redirect', $user) }}" class="...">
    Manage your subscription
</a>
```

Note that your can use any identifier you want as long as it's unique throughout your application. For example, if you want to provide team-based subscription, you can use `route('kanuu.redirect', $team)` or `route('kanuu.redirect', $team->uuid)`.

And that's it! ✨

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
