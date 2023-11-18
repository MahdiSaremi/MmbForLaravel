<?php

namespace Mmb\Laravel\Action;

use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Support\Caller\Caller;

abstract class Action
{

    public Update $update;

    public function __construct(
        Update $update = null,
    )
    {
        $this->update = $update ?? app(Update::class);
        $this->boot();
    }

    /**
     * Boot section
     *
     * @return void
     */
    protected function boot()
    {
    }

    /**
     * Invoke a method
     *
     * @param string $method
     * @param        ...$args
     * @return mixed
     */
    public function invoke(string $method, ...$args)
    {
        return Caller::invoke([$this, $method], $args, $this->getInvokeDynamicParameters($method));
    }

    /**
     * Invoke a method with dynamic parameters
     *
     * @param string $method
     * @param array  $normalArgs
     * @param array  $dynamicArgs
     * @return mixed
     */
    public function invokeDynamic(string $method, array $normalArgs, array $dynamicArgs)
    {
        return Caller::invoke([$this, $method], $normalArgs, $dynamicArgs + $this->getInvokeDynamicParameters($method));
    }

    /**
     * Get dynamic parameters when invoke a method
     *
     * @param string $method
     * @return array
     */
    protected function getInvokeDynamicParameters(string $method)
    {
        return [];
    }

}