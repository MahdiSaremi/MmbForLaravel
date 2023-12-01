<?php

namespace Mmb\Laravel\Action\Memory;

use Illuminate\Support\Facades\Facade;
use Mmb\Laravel\Support\Step\Stepping;

/**
 * @method static void setModel(Stepping $model)
 * @method static void set(StepHandler|ConvertableToStep|null $step)
 * @method static ?StepHandler get()
 */
class Step extends Facade
{

    protected static function getFacadeAccessor()
    {
        return StepFactory::class;
    }

}
