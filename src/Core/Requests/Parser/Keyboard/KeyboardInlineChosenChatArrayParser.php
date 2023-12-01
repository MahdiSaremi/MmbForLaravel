<?php

namespace Mmb\Laravel\Core\Requests\Parser\Keyboard;

use Mmb\Laravel\Core\Requests\Parser\ArrayParser;

class KeyboardInlineChosenChatArrayParser extends ArrayParser
{

    public function __construct()
    {
        parent::__construct(
            [
                'query'             => 'query',
                'allowUserChats'    => 'allowUserChats',
                'allowUser'         => 'allowUserChats',
                'allowBotChats'     => 'allowBotChats',
                'allowBot'          => 'allowBotChats',
                'allowGroupChats'   => 'allowGroupChats',
                'allowGroup'        => 'allowGroupChats',
                'allowChannelChats' => 'allowChannelChats',
                'allowChannel'      => 'allowChannelChats',
            ],
            errorOnFail: true,
        );
    }

}
