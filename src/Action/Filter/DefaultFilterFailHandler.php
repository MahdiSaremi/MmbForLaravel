<?php

namespace Mmb\Laravel\Action\Filter;

use Mmb\Laravel\Core\Updates\Update;

class DefaultFilterFailHandler
{

    public function handle(FilterFailException $exception, Update $update)
    {
        $update->response($exception->description);
    }

}
