<?php

namespace Mmb\Laravel\Core;

use ArrayObject;
use Traversable;

trait DataCollection
{

    public function __construct(array $data, Bot $bot = null, bool $trustedData = false)
    {
        $castTo = $this->getCollectionClassType();
        foreach($data as $i => $item)
        {
            $data[$i] = $this->castSingleData($item, $castTo, $trustedData);
        }

        $this->data = $data;
    }

    protected function dataCasts() : array
    {
        return [];
    }

    protected function dataRules() : array
    {
        return [];
    }

    protected abstract function getCollectionClassType();

    public function getDefault()
    {
        return $this->first();
    }

    public function get(int $index)
    {
        return $this->data[$index];
    }

    public function first()
    {
        return $this->data[0];
    }

    public function last()
    {
        return last($this->data);
    }

    public function count()
    {
        return count($this->data);
    }

    public function getIterator() : Traversable
    {
        return (new ArrayObject($this->data))->getIterator();
    }

    public function __get(string $name)
    {
        return $this->getDefault()->$name;
    }

    public function __set(string $name, $value) : void
    {
        $this->getDefault()->$name = $value;
    }

}