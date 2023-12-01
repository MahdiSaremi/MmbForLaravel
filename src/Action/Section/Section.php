<?php

namespace Mmb\Laravel\Action\Section;

use Mmb\Laravel\Action\Action;
use Mmb\Laravel\Action\Memory\Step;
use Mmb\Laravel\Core\Updates\Update;

class Section extends Action
{

    /**
     * Create new instance
     *
     * @param Update|null $update
     * @return static
     */
    public static function make(
        Update $update = null,
    )
    {
        return new static($update);
    }

    /**
     * Make menu from method
     *
     * @param string $name
     * @param mixed  ...$args
     * @return Menu
     */
    public function menu(string $name, ...$args)
    {
        return $this->initializeMenu($name, args: $args);
    }

    /**
     * Call the method in the next step
     *
     * @param string $method
     * @return void
     */
    public function nextStep(string $method)
    {
        NextStepHandler::make()->for(static::class, $method)->keep();
    }

}
