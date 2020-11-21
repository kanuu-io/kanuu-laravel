<?php

namespace Kanuu\Laravel;

use Illuminate\Routing\Controller;

class RedirectToKanuu extends Controller
{
    public function __invoke($identifier, Kanuu $kanuu)
    {
        return $kanuu->redirect($identifier);
    }
}
