<?php

namespace Mmb\Laravel\Action\Filter;

use Mmb\Laravel\Core\Updates\Update;

abstract class FilterRule
{

    /**
     * Pass update and check filter
     *
     * @param Update $update
     * @param        $value
     * @return void
     */
    public function pass(Update $update, &$value)
    {
    }

    /**
     * Set error message and return false
     *
     * @param $message
     * @throws FilterFailException
     */
    public function fail($message)
    {
        throw new FilterFailException($message, "Filter [".class_basename(static::class)."] failed");
    }

}
