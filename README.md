# Integrate Kanuu to your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kanuu-io/kanuu-laravel.svg)](https://packagist.org/packages/kanuu-io/kanuu-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/kanuu-io/kanuu-laravel/Tests?label=tests)](https://github.com/kanuu-io/kanuu-laravel/actions?query=workflow%3ATests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kanuu-io/kanuu-laravel.svg)](https://packagist.org/packages/kanuu-io/kanuu-laravel)

🛶 Quickly integrate your Laravel application with Kanuu in 3 simples steps.

## Installation

```bash
composer require kanuu-io/kanuu-laravel
```

## Getting started

First, add your Kanuu API key to your `.env` file.

```php
KANUU_API_KEY="YOUR_API_KEY_HERE"
```

Next, add a route that will redirect your user to Kanuu based on a unique identifier.

```php
Route::get('kannu/{identifier}', '\Kanuu\Laravel\RedirectToKanuu')->name('kanuu.redirect');
```

Now, all you need to do is add a "Manage your subscription" button that uses that route.

```html
<a href="{{ route('kanuu.redirect') }}" class="...">
    Manage your subscription
</a>
```

And that's it! ✨

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
