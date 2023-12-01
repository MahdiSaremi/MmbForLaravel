<?php

namespace Mmb\Laravel\Action\Filter\Rules;

use Mmb\Laravel\Core\Updates\Update;

class BeTextSingleLine extends BeText
{

    public function __construct(
        public $singleLineError = null,
        $textError = null,
        $messageError = null
    )
    {
        parent::__construct($textError, $messageError);
    }

    public function pass(Update $update, &$value)
    {
        parent::pass($update, $value);

        if(str_contains($value, "\n"))
        {
            $this->fail(value($this->singleLineError ?? __('filter.text-single-line')));
        }
    }

}
