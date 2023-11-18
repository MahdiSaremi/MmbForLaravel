<?php

namespace Mmb\Laravel\Action\Memory;

use Mmb\Laravel\Action\Memory\Attributes\StepHandlerAttribute;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

class StepHandler
{

    public function __construct(
        ?StepMemory $memory = null,
    )
    {
        if($memory)
        {
            $this->load($memory);
        }
    }

    public static function make(?StepMemory $memory = null)
    {
        return new static($memory);
    }

    /**
     * Get attributes of property
     *
     * @param ReflectionProperty $property
     * @return ReflectionAttribute[]
     */
    protected function getAttributesOf(ReflectionProperty $property)
    {
        $attrs = $property->getAttributes();
        foreach($attrs as $i => $attr)
        {
            $attr = $attr->newInstance();
            if(!($attr instanceof StepHandlerAttribute))
            {
                unset($attrs[$i]);
            }
            $attrs[$i] = $attr;
        }

        return $attrs;
    }

    /**
     * Load data from memory
     *
     * @param StepMemory $memory
     * @return void
     */
    public function load(StepMemory $memory)
    {
        $ref = new ReflectionClass($this);
        foreach($ref->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
        {
            StepHandlerAttribute::load(
                $this->getAttributesOf($property),
                $property->getName(),
                $memory,
                $this
            );
        }
    }

    /**
     * Save data to memory
     *
     * @param StepMemory $memory
     * @return void
     */
    public function save(StepMemory $memory)
    {
        $ref = new ReflectionClass($this);
        foreach($ref->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
        {
            StepHandlerAttribute::save(
                $this->getAttributesOf($property),
                $property->getName(),
                $memory,
                $this
            );
        }
    }

    /**
     * Store step to user model
     *
     * @return void
     */
    public function keep()
    {
        // TODO
    }

}