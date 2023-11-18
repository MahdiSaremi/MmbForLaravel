<?php

namespace Mmb\Laravel\Core\Updates\Files;

/**
 * @property int    $length
 * @property int    $duration
 * @property ?Photo $thumbnail
 *
 * @property int    $width
 * @property int    $height
 */
class VideoNote extends DataWithFile
{

    protected function dataCasts() : array
    {
        return [
                'length'    => 'int',
                'duration'  => 'int',
                'thumbnail' => Photo::class,
            ] + parent::dataCasts();
    }

    protected function getWidthAttribute()
    {
        return $this->data['length'];
    }

    protected function getHeightAttribute()
    {
        return $this->data['length'];
    }

}