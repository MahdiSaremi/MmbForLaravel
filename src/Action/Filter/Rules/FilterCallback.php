<?php

namespace Mmb\Laravel\Action\Filter\Rules;

use Closure;
use Mmb\Laravel\Action\Filter\FilterRule;
use Mmb\Laravel\Core\Updates\Update;

class FilterCallback extends FilterRule
{

    public function __construct(
        public Closure $callback,
    )
    {
    }

    public function pass(Update $update, &$value)
    {
        $fn = $this->callback;
        $fn($update, $value, $this);
    }

}
