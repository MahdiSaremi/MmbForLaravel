<?php

namespace Mmb\Laravel\Core\Updates\Poll;

use Mmb\Laravel\Core\Data;

/**
 * @property string $text
 * @property int    $voterCount
 */
class PollOption extends Data
{

    protected function dataCasts() : array
    {
        return [
            'text'        => 'string',
            'voter_count' => 'int',
        ];
    }

}