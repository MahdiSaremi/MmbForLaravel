<?php

namespace Mmb\Laravel;

use Illuminate\Support\Facades\Facade;
use Mmb\Laravel\Core\Bot;

class Mmb extends Facade
{

    protected static function getFacadeAccessor()
    {
        return Bot::class;
    }

}