<?php

namespace Mmb\Laravel\Action\Form;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use TypeError;

class FormKeyBuilder implements Arrayable
{

    public function __construct(
        public Form $form,
    )
    {
    }

    public array $optionsAll = [];
    public array $footersAll = [];
    public array $headersAll = [];

    private function convertOptions($options)
    {
        if($options instanceof Closure)
        {
            $options = iterator_to_array($options());
        }

        return $options;
    }

    public function schema(array|Closure $options)
    {
        array_push($this->optionsAll, ...$this->convertOptions($options));
    }

    public function footer(array|Closure $options)
    {
        array_push($this->footersAll, ...$this->convertOptions($options));
    }

    public function header(array|Closure $options)
    {
        array_push($this->headersAll, ...$this->convertOptions($options));
    }


    /**
     * Convert key builder to array
     *
     * @return array
     */
    public function toArray()
    {
        $key = [];

        array_push($key, ...$this->partToArray($this->headersAll));
        array_push($key, ...$this->partToArray($this->optionsAll));
        array_push($key, ...$this->partToArray($this->footersAll));

        return $key;
    }

    /**
     * Convert a part to array
     *
     * @param array $array
     * @return array
     */
    protected function partToArray(array $array)
    {
        $result = [];
        foreach($array as $row)
        {
            if($row === null)
            {
                continue;
            }

            if(!is_array($row))
            {
                throw new TypeError(sprintf("Invalid type, key row should be [array], given [%s]", smartTypeOf($row)));
            }

            $resultRow = [];
            foreach($row as $key)
            {
                if($key === null)
                {
                    continue;
                }

                if(!($key instanceof FormKey))
                {
                    throw new TypeError(sprintf("Invalid type, key should be [%s], given [%s]", FormKey::class, smartTypeOf($key)));
                }

                if(!$key->enabled)
                {
                    continue;
                }

                $resultRow[] = ['text' => $key->text];
            }

            if($resultRow)
            {
                $result[] = $resultRow;
            }
        }

        return $result;
    }


    /**
     * Convert key value to FormKey
     *
     * @param $key
     * @return FormKey
     */
    protected function toKey($key)
    {
        if($key instanceof FormKey)
        {
            return $key;
        }

        if(is_string($key))
        {
            return FormKey::make($key);
        }

        throw new TypeError(sprintf("Expected [%s], given [%s]", FormKey::class, smartTypeOf($key)));
    }

    /**
     * Convert key list to FormKey[]
     *
     * @param $key
     * @return FormKey[]
     */
    protected function toKeyLine($key)
    {
        if(!is_array($key))
        {
            if(is_iterable($key))
            {
                $key = iterator_to_array($key);
            }
            else
            {
                $key = [$key];
            }
        }

        foreach($key as $index => $subKey)
        {
            if($subKey === null)
            {
                unset($key[$index]);
            }
            else
            {
                $key[$index] = $this->toKey($subKey);
            }
        }

        return array_values($key);
    }


    /**
     * Add new empty line
     *
     * @return $this
     */
    public function break()
    {
        $this->optionsAll[] = [];

        return $this;
    }

    /**
     * Add key row
     *
     * @param array|FormKey|string $key
     * @param                      $value
     * @return $this
     */
    public function add(array|FormKey|string $key, $value = null)
    {
        if(is_string($key) && count(func_get_args()) > 1)
        {
            $key = FormKey::make($key, $value);
        }

        if(is_array($key))
        {
            $this->break();
            return $this->push($key);
        }

        $this->optionsAll[] = [$key];
        return $this;
    }

    /**
     * Add key to last row
     *
     * @param array|FormKey|string $key
     * @param                      $value
     * @return $this
     */
    public function push(array|FormKey|string $key, $value = null)
    {
        if(is_string($key) && count(func_get_args()) > 1)
        {
            $key = [ FormKey::make($key, $value) ];
        }

        if(!$this->optionsAll)
        {
            $this->optionsAll = [];
        }

        array_push($this->optionsAll[array_key_last($this->optionsAll)], ...$this->toKeyLine($key));
        return $this;
    }

}
