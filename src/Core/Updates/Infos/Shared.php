<?php

namespace Mmb\Laravel\Core\Updates\Infos;

use Mmb\Laravel\Core\Data;

/**
 * @property int $requestId
 */
abstract class Shared extends Data
{

    protected function dataCasts() : array
    {
        return [
            'request_id' => 'int',
        ];
    }

}