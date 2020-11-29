# Integrate Kanuu to your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kanuu-io/kanuu-laravel.svg)](https://packagist.org/packages/kanuu-io/kanuu-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/kanuu-io/kanuu-laravel/Tests?label=tests)](https://github.com/kanuu-io/kanuu-laravel/actions?query=workflow%3ATests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kanuu-io/kanuu-laravel.svg)](https://packagist.org/packages/kanuu-io/kanuu-laravel)

🛶 Quickly integrate your Laravel application with Kanuu in **3 simple steps**.

## Installation

```bash
composer require kanuu-io/kanuu-laravel
```

## Documentation

:books: Read the full documentation at [docs.kanuu.io](https://docs.kanuu.io/laravel/installation.html)

## Basic usage

##### 1. Add your Kanuu API key to your `.env` file.

```php
KANUU_API_KEY="YOUR_API_KEY"
```

#### 2. Add a route to your `routes/web.php` that will redirect your user to Kanuu based on a unique identifier.

```php
use Kanuu\Laravel\Facades\Kanuu;

// ...

Kanuu::redirectRoute()->name('kanuu.redirect');
```

#### 3. Add a "Manage your subscription" button that uses that route.

```html
<a href="{{ route('kanuu.redirect', $user) }}" class="...">
    Manage your subscription
</a>
```

Note that your can use any identifier you want as long as it's unique throughout your application. For example, if you want to provide team-based subscription, you can use `route('kanuu.redirect', $team)` or `route('kanuu.redirect', $team->uuid)`.

And that's it! ✨

## Advanced usage

Kanuu's package provide a lot more for you to get started using Kanuu and Paddle. This includes:
- [A `HandleWebhookController`](https://docs.kanuu.io/laravel/webhook-helpers.html) that takes care of both handling Paddle's webhooks and verifying their signature.
- [A `kanuu:publish` command](https://docs.kanuu.io/laravel/subscription-boilerplate.html) that provides all the boilerplate you need to get started with billing.

<sub>_Full documentation available at [docs.kanuu.io](https://docs.kanuu.io/laravel/installation.html)_</sub>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
