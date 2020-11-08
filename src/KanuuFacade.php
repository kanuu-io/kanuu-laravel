<?php

namespace Kanuu\Laravel;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kanuu\Laravel\Kanuu
 */
class KanuuFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'kanuu';
    }
}
