<?php

namespace Mmb\Laravel\Core\Updates\Files;

use Mmb\Laravel\Core\Data;

/**
 * @property string  $id
 * @property string  $uniqueId
 * @property ?int    $size
 * @property ?string $filePath
 */
class FileInfo extends Data
{

    protected function dataCasts() : array
    {
        return [
            'file_id'        => 'string',
            'file_unique_id' => 'string',
            'file_size'      => 'int',
            'file_path'      => 'string',
        ];
    }

    protected function dataShortAccess() : array
    {
        return [
            'id'        => 'file_id',
            'unique_id' => 'file_unique_id',
            'size'      => 'file_size',
        ];
    }

    // TODO
    public function getDownloadUrl()
    {
        return "https://api.telegram.org/file/bot{$this->bot()->token}/{$this->filePath}";
    }

}