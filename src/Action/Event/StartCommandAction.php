<?php

namespace Mmb\Laravel\Action\Event;

abstract class StartCommandAction extends CommandAction
{

    protected $command = '/start';

    public abstract function handle();

}
