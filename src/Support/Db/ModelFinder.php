<?php

namespace Mmb\Laravel\Support\Db;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Model find(string $model, $id, $or = null)
 * @method static Model findBy(string $model, string $key, $id, $or = null)
 * @method static Model findOrFail(string $model, $id)
 * @method static Model store(Model $model)
 * @method static void forget(string|Model $model)
 * @method static void clear()
 * @method static Model storeCurrent(Model $model)
 * @method static Model current(string $model)
 */
class ModelFinder extends Facade
{

    protected static function getFacadeAccessor()
    {
        return FinderFactory::class;
    }

}
