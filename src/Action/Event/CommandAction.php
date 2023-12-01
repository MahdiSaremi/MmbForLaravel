<?php

namespace Mmb\Laravel\Action\Event;

use Mmb\Laravel\Action\Action;
use Mmb\Laravel\Action\Update\UpdateHandling;
use Mmb\Laravel\Core\Updates\Update;

class CommandAction extends Action implements UpdateHandling
{

    protected $command;

    /**
     * Get command
     *
     * @return string|array
     */
    public function getCommand()
    {
        return $this->command;
    }

    public function handleUpdate(Update $update)
    {
        if($update->message?->isCommand($this->getCommand(), $prompts))
        {
            $this->invoke('handle', ...$prompts);
            return;
        }

        $update->skipHandler();
    }
}
