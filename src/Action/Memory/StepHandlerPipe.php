<?php

namespace Mmb\Laravel\Action\Memory;

use Mmb\Laravel\Action\Update\UpdateHandling;
use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Support\Step\Stepping;

class StepHandlerPipe implements UpdateHandling
{

    public function __construct(
        public Stepping $stepping,
    )
    {
    }

    public function handleUpdate(Update $update)
    {
        if($step = $this->stepping->getStep())
        {
            $step->handle($update);
            return; // TODO
        }

        $update->skipHandler();
    }

}
