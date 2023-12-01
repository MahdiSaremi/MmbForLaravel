<?php

namespace Mmb\Laravel\Support\Db\Attributes;

use Attribute;
use Mmb\Laravel\Support\Caller\CallingAttribute;
use Mmb\Laravel\Support\Db\ModelFinder;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FindBy extends CallingAttribute
{

    public function __construct(
        public string $key,
        public ?int $error = 404,
    )
    {
    }

    public function cast($value, string $class)
    {
        if($value instanceof $class || $value === null)
        {
            return $value;
        }

        if($this->error)
        {
            return ModelFinder::findBy($class, $this->key, $value, fn() => abort($this->error));
        }
        else
        {
            return ModelFinder::findBy($class, $this->key, $value);
        }
    }

}
