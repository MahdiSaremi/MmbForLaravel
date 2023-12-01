<?php

namespace Mmb\Laravel\Core\Requests\Parser\Keyboard;

use Mmb\Laravel\Core\Requests\Parser\ArrayParser;

class KeyboardRequestPollArrayParser extends ArrayParser
{

    public function __construct()
    {
        parent::__construct(
            [
                'type' => 'type',
            ],
            errorOnFail: true,
        );
    }

}
