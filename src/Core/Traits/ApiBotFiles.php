<?php

namespace Mmb\Laravel\Core\Traits;

use Mmb\Laravel\Core\Updates\Files\FileInfo;

trait ApiBotFiles
{

    public function getFile(array $args = [], ...$namedArgs)
    {
        return $this->makeData(
            FileInfo::class,
            $this->request('getFile', $args + $namedArgs)
        );
    }

    public function getFileDownloadUrl(string $filePath)
    {
        return "https://api.telegram.org/file/bot" . $this->token . "/" . $filePath;
    }

}
