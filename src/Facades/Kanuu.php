<?php

namespace Kanuu\Laravel\Facades;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Kanuu\Laravel\Kanuu
 * @method static array getNonce(mixed $identifier)
 * @method static RedirectResponse redirect(mixed $identifier)
 */
class Kanuu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'kanuu';
    }
}
