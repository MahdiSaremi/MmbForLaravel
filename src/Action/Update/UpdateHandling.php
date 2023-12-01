<?php

namespace Mmb\Laravel\Action\Update;

use Mmb\Laravel\Core\Updates\Update;

interface UpdateHandling
{

    public function handleUpdate(Update $update);

}
