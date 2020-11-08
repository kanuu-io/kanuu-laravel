<?php

namespace Kanuu\Laravel;

use Illuminate\Routing\Controller;
use Kanuu\Laravel\Facades\Kanuu as KanuuFacade;

class RedirectToKanuu extends Controller
{
    public function __invoke($identifier)
    {
        return KanuuFacade::redirect($identifier);
    }
}
