<?php

namespace Mmb\Laravel\Support\Step;

use Mmb\Laravel\Action\Memory\StepHandler;

interface Stepping
{

    /**
     * Get current step
     *
     * @return ?StepHandler
     */
    public function getStep() : ?StepHandler;

    /**
     * Set current step
     *
     * @param ?StepHandler $stepHandler
     * @return mixed
     */
    public function setStep(?StepHandler $stepHandler);

}
