<?php

namespace Mmb\Laravel\Action\Memory;

interface ConvertableToStep
{

    public function toStep() : ?StepHandler;

}
