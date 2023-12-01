<?php

namespace Mmb\Laravel\Core\Requests\Parser\Keyboard;

use Mmb\Laravel\Core\Requests\Parser\ArrayParser;

class KeyboardRequestUserArrayParser extends ArrayParser
{

    public function __construct()
    {
        parent::__construct(
            [
                'id'            => 'requestId',
                'requestId'     => 'requestId',
                'userIsBot'     => 'userIsBot',
                'isBot'         => 'userIsBot',
                'userIsPremium' => 'userIsPremium',
                'isPremium'     => 'userIsPremium',
            ],
            errorOnFail: true,
        );
    }

}
