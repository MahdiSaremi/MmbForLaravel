<?php

namespace Mmb\Laravel\Core;

use Illuminate\Support\Traits\Macroable;
use Mmb\Laravel\Core\Requests\HasRequest;
use Mmb\Laravel\Core\Traits\ApiBotInfos;
use Mmb\Laravel\Core\Traits\ApiBotMessages;
use Mmb\Laravel\Core\Updates\Update;

class Bot
{
    use HasRequest,
        Macroable,
        ApiBotInfos,
        ApiBotMessages;

    public function __construct(
        private string $token,
    )
    {
    }

    public function getUpdate()
    {
        if($update = request()->json())
        {
            return Update::make($update, $this, true);
        }
        else
        {
            return false;
        }
    }

    /**
     * Create data
     *
     * @template T
     * @param class-string<T> $class
     * @param                 $data
     * @param bool            $trustedData
     * @return ?T
     */
    public function makeData(string $class, $data, bool $trustedData = true)
    {
        if($data === null || $data === false)
        {
            return null;
        }

        return $class::make($data, $this, $trustedData);
    }

}