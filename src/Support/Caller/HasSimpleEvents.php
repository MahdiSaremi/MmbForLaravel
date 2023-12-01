<?php

namespace Mmb\Laravel\Support\Caller;

use Closure;

trait HasSimpleEvents
{

    /**
     * Events
     *
     * @var array
     */
    private array $_events = [];

    /**
     * Add event listener
     *
     * @param string  $event
     * @param Closure $callback
     * @return void
     */
    public function on(string $event, Closure $callback)
    {
        @$this->_events[strtolower($event)][] = $callback;
    }

    /**
     * Fire event
     *
     * @param string $event
     * @param        ...$args
     * @return bool
     */
    public function fire(string $event, ...$args)
    {
        $event = strtolower($event);
        foreach($this->_events[$event] ?? [] as $listener)
        {
            if($listener(...$args) === true)
            {
                return true;
            }
        }

        if(method_exists($this, 'on' . $event))
        {
            return (bool) $this->{'on' . $event}(...$args);
        }

        return false;
    }

}
