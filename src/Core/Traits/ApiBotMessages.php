<?php

namespace Mmb\Laravel\Core\Traits;

use Mmb\Laravel\Core\Builder\ApiMessageBuilder;
use Mmb\Laravel\Core\Updates\Messages\Message;

trait ApiBotMessages
{

    public function newMessage()
    {
        return ApiMessageBuilder::make($this);
    }

    public function sendMessage(array $args = [])
    {
        return $this->makeData(
            Message::class,
            $this->request('sendMessage', $args)
        );
    }

}