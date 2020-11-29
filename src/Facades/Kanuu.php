<?php

namespace Kanuu\Laravel\Facades;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Kanuu\Laravel\Kanuu
 * @method static array getNonce(mixed $identifier)
 * @method static RedirectResponse redirect(mixed $identifier)
 * @method static string getIdentifier(mixed $identifier)
 * @method static \Kanuu\Laravel\Kanuu getModelUsing(Closure $modelResolver)
 * @method static \Kanuu\Laravel\Kanuu on(string $event, Closure $webhookHandler)
 * @method static Route redirectRoute(string $url = 'kanuu/{identifier}')
 * @method static Route webhookRoute(string $url = 'webhooks/paddle')
 */
class Kanuu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'kanuu';
    }
}
