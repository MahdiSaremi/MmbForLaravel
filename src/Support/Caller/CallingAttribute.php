<?php

namespace Mmb\Laravel\Support\Caller;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class CallingAttribute
{

    public function cast($value, string $class)
    {
        return $value;
    }

}
