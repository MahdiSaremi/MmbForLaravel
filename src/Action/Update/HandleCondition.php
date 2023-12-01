<?php

namespace Mmb\Laravel\Action\Update;

use Mmb\Laravel\Core\Bot;
use Mmb\Laravel\Core\Updates\Update;

abstract class HandleCondition
{

    public Update $update;

    public Bot $bot;

    public function __construct(
        public $action,
    )
    {
    }

    public function check()
    {
        return false;
    }

    public function handle()
    {
        return false;
    }

}
