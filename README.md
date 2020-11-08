# Integrate Kanuu to your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kanuu-io/kanuu-laravel.svg)](https://packagist.org/packages/kanuu-io/kanuu-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/kanuu-io/kanuu-laravel/Tests?label=tests)](https://github.com/kanuu-io/kanuu-laravel/actions?query=workflow%3ATests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kanuu-io/kanuu-laravel.svg)](https://packagist.org/packages/kanuu-io/kanuu-laravel)

🛶 Quickly integrate your Laravel application with Kanuu in **3 simple steps**.

## Installation

```bash
composer require kanuu-io/kanuu-laravel
```

## Getting started

##### 1. Add your Kanuu API key to your `.env` file.

```php
KANUU_API_KEY="YOUR_API_KEY_HERE"
```

##### 2. Add a route that will redirect your user to Kanuu based on a unique identifier.

```php
Route::get('kannu/{identifier}', '\Kanuu\Laravel\RedirectToKanuu')->name('kanuu.redirect');
```

##### 3. Now, all you need to do is add a "Manage your subscription" button that uses that route.

```html
<a href="{{ route('kanuu.redirect', $user) }}" class="...">
    Manage your subscription
</a>
```

Note that your can use any identifier you want as long as it's unique throughout your application. For example, if you want to provide team-based subscription, you can use `route('kanuu.redirect', $team)` or `route('kanuu.redirect', $team->uuid)`.

And that's it! ✨

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
