<?php

namespace Mmb\Laravel\Action\Section;

use Mmb\Laravel\Action\Memory\Attributes\StepHandlerAlias as Alias;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerSafeClass as SafeClass;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerShortClass as ShortClass;
use Mmb\Laravel\Action\Memory\StepHandler;
use Mmb\Laravel\Action\Memory\StepMemory;
use Mmb\Laravel\Core\Updates\Update;

class NextStepHandler extends StepHandler
{

    #[Alias('c')]
    #[ShortClass('App\\Mmb\\Sections\\', '*')]
    #[SafeClass('')]
    public string $action;

    #[Alias('m')]
    public string $method;

    public function for(string|array $action, string $method = null)
    {
        if(is_array($action))
        {
            [$this->action, $this->method] = $action;
        }
        else
        {
            $this->action = $action;
            $this->method = $method;
        }
    }

    public function handle(Update $update)
    {
        if(class_exists($this->action) && method_exists($this->action, 'make'))
        {
            $action = $this->action::make($update);

            // if($action)
            // TODO
        }
    }

}