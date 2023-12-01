<?php

namespace Mmb\Laravel\Action\Filter\Rules;

use Mmb\Laravel\Action\Filter\FilterRule;
use Mmb\Laravel\Core\Updates\Update;

class BeMessage extends FilterRule
{

    public function __construct(
        public $messageError = null,
    )
    {
    }

    public function pass(Update $update, &$value)
    {
        if(!$update->message)
        {
            $this->fail(value($this->messageError ?? __('filter.message')));
        }

        $value = $update->message;
    }

}
