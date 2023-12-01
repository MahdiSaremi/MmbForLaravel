<?php

namespace Mmb\Laravel\Core\Requests\Parser;

use Illuminate\Support\Facades\Facade;
use Mmb\Laravel\Core\Requests\RequestApi;

/**
 * @method static void on(string $name, $value)
 * @method static void onMethod(string $name, string|array $method, $value)
 * @method static void merge(array ...$items)
 * @method static array normalize(RequestApi $request)
 */
class ArgsParser extends Facade
{

    protected static function getFacadeAccessor()
    {
        return ArgsParserFactory::class;
    }

}
