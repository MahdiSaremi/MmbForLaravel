<?php

namespace Mmb\Laravel\Core\Updates\Files;

use Mmb\Laravel\Core\Data;

/**
 * @property string $id
 * @property string $uniqueId
 * @property ?int   $size
 */
abstract class DataWithFile extends Data
{

    protected function dataCasts() : array
    {
        return [
            'file_id'        => 'string',
            'file_unique_id' => 'string',
            'file_size'      => 'int',
        ];
    }

    protected function dataShortAccess() : array
    {
        return [
            'id'        => 'file_id',
            'unique_id' => 'file_id',
            'size'      => 'file_size',
        ];
    }

}