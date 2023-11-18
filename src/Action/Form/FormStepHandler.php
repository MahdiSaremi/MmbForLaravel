<?php

namespace Mmb\Laravel\Action\Form;

use Mmb\Laravel\Action\Memory\Attributes\StepHandlerAlias as Alias;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerSafeClass as SafeClass;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerSerialize as Serialize;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerShortClass as ShortClass;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerInstead as Instead;
use Mmb\Laravel\Action\Memory\StepHandler;

class FormStepHandler extends StepHandler
{

    #[Alias('c')]
    #[ShortClass('Mmb\\Laravel\\', '$')]
    #[SafeClass('')]
    public string $class;

    #[Alias('i')]
    public string $currentInput;

    #[Alias('T')]
    #[Instead([
        'Normal' => 0,
        'Fixed'  => 1,
    ])]
    public string $type;

}