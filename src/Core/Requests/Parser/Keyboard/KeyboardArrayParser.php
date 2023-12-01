<?php

namespace Mmb\Laravel\Core\Requests\Parser\Keyboard;

use Mmb\Laravel\Core\Requests\Parser\ArrayParser;

class KeyboardArrayParser extends ArrayParser
{

    public function __construct()
    {
        parent::__construct(
            [
                0                 => 'text',
                'text'            => 'text',
                'requestUser'     => app(KeyboardRequestUserArrayParser::class),
                'requestChat'     => app(KeyboardRequestChatArrayParser::class),
                'requestPoll'     => app(KeyboardRequestPollArrayParser::class),
                'requestLocation' => 'requestLocation',
                'requestContact'  => 'requestContact',
                'webApp'          => 'webApp', // TODO

                'url'          => 'url',
                'callbackData' => 'callbackData',
                'data'         => 'callbackData',

                'switchInlineQuery'            => 'switchInlineQuery',
                'inline'                       => 'switchInlineQuery',
                'switchInlineQueryCurrentChat' => 'switchInlineQueryCurrentChat',
                'inlineCurrentChat'            => 'switchInlineQueryCurrentChat',
                'inlineCurrent'                => 'switchInlineQueryCurrentChat',

                'switchInlineQueryChosenChat' => $this->parseSwitchChosen(...),
                'inlineChosenChat'            => $this->parseSwitchChosen(...),
                'inlineChosen'                => $this->parseSwitchChosen(...),
            ],
            errorOnFail: true,
        );
    }

    public function parseSwitchChosen($key, $value, $real)
    {
        return [
            'switch_inline_query_chosen_chat' => app(KeyboardInlineChosenChatArrayParser::class)->normalize($value),
        ];
    }

}
