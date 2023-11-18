<?php

namespace Mmb\Laravel\Tests;

use Mmb\Laravel\Core\Bot;
use Mmb\Laravel\Core\Requests\TelegramRequest;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        $this->createApplication();
    }

    public function createApplication()
    {
        app()->singleton(Bot::class, fn() => new Bot('1307165749:AAELc518VsivWkwMBOu2I6PHoEm1R0hF-Io'));
        TelegramRequest::appendOptions([
            'proxy' => '192.168.30.126:10809',
        ]);
    }

    public function bot() : Bot
    {
        return app(Bot::class);
    }
}
