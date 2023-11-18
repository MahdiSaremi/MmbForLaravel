<?php

namespace Mmb\Laravel\Core\Updates\Data;

use Mmb\Laravel\Core\Data;

/**
 * @property string $emoji
 * @property string $value
 */
class Dice extends Data
{

    protected function dataCasts() : array
    {
        return [
            'emoji' => 'string',
            'value' => 'string',
        ];
    }

}