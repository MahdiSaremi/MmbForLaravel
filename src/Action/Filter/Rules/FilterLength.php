<?php

namespace Mmb\Laravel\Action\Filter\Rules;

use Mmb\Laravel\Action\Filter\FilterRule;
use Mmb\Laravel\Core\Updates\Update;

class FilterLength extends FilterRule
{

    public function __construct(
        public $min = null,
        public $max = null,
        public $minError = null,
        public $maxError = null,
        public $error = null,
        public bool $ascii = true
    )
    {
    }

    public function pass(Update $update, &$value)
    {
        if(!is_string($value) && !is_int($value) && !is_float($value))
        {
            $this->fail(__('filter.string-able'));
        }

        $length = $this->ascii ? mb_strlen($value) : strlen($value);
        $min = value($this->min);
        $max = value($this->max);

        // Minimum check
        if(isset($this->min) && $length < $min)
        {
            if($this->minError === null && $this->error !== null)
            {
                $this->fail(sprintf(value($this->error, $min, $max), $min, $max));
            }
            else
            {
                $this->fail(sprintf(value($this->minError ?? __('filter.min-length', ['length' => $min])), $min));
            }
        }

        // Maximum check
        if(isset($this->max) && $length > $max)
        {
            if($this->maxError === null && $this->error !== null)
            {
                $this->fail(sprintf(value($this->error, $min, $max), $min, $max));
            }
            else
            {
                $this->fail(sprintf(value($this->maxError ?? __('filter.max-length', ['length' => $max])), $max));
            }
        }
    }

}
