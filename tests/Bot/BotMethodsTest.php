<?php

namespace Mmb\Laravel\Tests\Bot;

use Mmb\Laravel\Action\Form\FormStepHandler;
use Mmb\Laravel\Action\Memory\StepMemory;
use Mmb\Laravel\Core\Bot;
use Mmb\Laravel\Core\Updates\Messages\Message;
use Mmb\Laravel\Support\Caller\Caller;
use Mmb\Laravel\Support\Caller\CallerFactory;
use Mmb\Laravel\Tests\TestCase;

class BotMethodsTest extends TestCase
{

    public function test_get_me_method()
    {
        $message = Message::make([
            'message_id' => 12,
            'chat' => [
                'id' => 370924007,
            ]
        ]);

        dd(
            $this->bot()
                ->newMessage()
                ->reply($message)
                ->html('<b>Hello World</b>')
                ->send()
        );
    }

    public function test2()
    {
        $handler = new FormStepHandler();
        $handler->class = static::class;
        $handler->currentInput = 'demo';
        $handler->type = 'Fixed';

        $handler->save($memory = new StepMemory);
        // dd($memory);

        $handler = new FormStepHandler($memory);
        dd($handler);
    }

}