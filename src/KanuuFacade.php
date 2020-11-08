<?php

namespace Kanuu\Laravel;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @see Kanuu
 * @method static array getNonce(mixed $identifier)
 * @method static RedirectResponse redirect(mixed $identifier)
 */
class KanuuFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'kanuu';
    }
}
