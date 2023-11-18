<?php

namespace Mmb\Laravel\Core\Updates\Infos;

use Mmb\Laravel\Core\Data;
use Mmb\Laravel\Core\Updates\Files\PhotoCollection;

/**
 * @property int             $totalCount
 * @property PhotoCollection $photos
 */
class UserProfilePhotos extends Data
{

    protected function dataCasts() : array
    {
        return [
            'total_count' => 'int',
            'photos'      => PhotoCollection::class,
        ];
    }

}