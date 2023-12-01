<?php

namespace Mmb\Laravel\Action\Filter\Rules;

use Mmb\Laravel\Action\Filter\FilterRule;
use Mmb\Laravel\Core\Updates\Update;

class FilterClamp extends FilterRule
{

    public function __construct(
        public $min = null,
        public $max = null,
        public $minError = null,
        public $maxError = null,
        public $error = null,
    )
    {
    }

    public function pass(Update $update, &$value)
    {
        if(!is_numeric($value))
        {
            $this->fail(__('filter.numeric'));
        }

        $number = +$value;
        $min = value($this->min);
        $max = value($this->max);

        // Minimum check
        if(isset($this->min) && $number < $min)
        {
            if($this->minError === null && $this->error !== null)
            {
                $this->fail(sprintf(value($this->error, $min, $max), $min, $max));
            }
            else
            {
                $this->fail(sprintf(value($this->minError ?? __('filter.min', ['number' => $min])), $min));
            }
        }

        // Maximum check
        if(isset($this->max) && $number > $max)
        {
            if($this->maxError === null && $this->error !== null)
            {
                $this->fail(sprintf(value($this->error, $min, $max), $min, $max));
            }
            else
            {
                $this->fail(sprintf(value($this->maxError ?? __('filter.max', ['number' => $max])), $max));
            }
        }
    }

}
