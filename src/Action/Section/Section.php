<?php

namespace Mmb\Laravel\Action\Section;

use Mmb\Laravel\Action\Action;
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

}