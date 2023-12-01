<?php

namespace Mmb\Laravel\Action\Form;

use Mmb\Laravel\Action\Memory\Attributes\StepHandlerAlias as Alias;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerSafeClass as SafeClass;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerSerialize as Serialize;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerShortClass as ShortClass;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerInstead as Instead;
use Mmb\Laravel\Action\Memory\StepHandler;
use Mmb\Laravel\Core\Updates\Update;

class FormStepHandler extends StepHandler
{

    #[Alias('C')]
    #[SafeClass]
    public $class;

    #[Alias('i')]
    public $currentInput;

    #[Alias('a')]
    #[Serialize]
    public $attributes;

    public function handle(Update $update)
    {
        if($this->class && is_a($this->class, Form::class, true))
        {
            /** @var Form $form */
            $form = new $this->class($update);
            $form->loadStepHandler($this);
            $form->continueForm();

            return true;
        }

        return false;
    }

}
