<?php

namespace Mmb\Laravel\Support\Exceptions;

use Mmb\Laravel\Core\Updates\Update;

interface CallableException
{

    public function invoke(Update $update);

}
