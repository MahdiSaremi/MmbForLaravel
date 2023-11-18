<?php

namespace Mmb\Laravel\Support\Caller;

use Illuminate\Support\Facades\Facade;

class Caller extends Facade
{

    protected static function getFacadeAccessor()
    {
        return CallerFactory::class;
    }

}