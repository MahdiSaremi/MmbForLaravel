<?php

namespace Mmb\Laravel\Action\Event;

abstract class InviteCommandAction extends CommandAction
{

    protected $command = '/start {id:\d+}';

    public abstract function handle($id);

}
