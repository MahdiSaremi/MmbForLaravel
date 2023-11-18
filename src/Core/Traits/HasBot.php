<?php

namespace Mmb\Laravel\Core\Traits;

use Mmb\Laravel\Core\Bot;

trait HasBot
{

    private ?Bot $targetBot = null;

    public function setTargetBot(?Bot $bot)
    {
        $this->targetBot = $bot;
    }

    public function bot() : Bot
    {
        return $this->targetBot ?? app(Bot::class);
    }

}