<?php

namespace Mmb\Laravel\Action\Form;

class FormKey
{

    public function __construct(
        public string $text,
    )
    {
    }

    /**
     * Make new form key
     *
     * @param string $text
     * @param        $value
     * @return static
     */
    public static function make(string $text, $value = null)
    {
        $key = new static($text);

        if(count(func_get_args()) > 1)
        {
            $key->value($value);
        }

        return $key;
    }

    /**
     * Make new form key with custom action
     *
     * @param string $text
     * @param        $action
     * @return FormKey
     */
    public static function makeAction(string $text, $action)
    {
        return (new static($text))->action($action);
    }


    public const TYPE_NORMAL = 1;
    public const TYPE_VALUE  = 2;
    public const TYPE_ACTION = 3;

    public int $type = 1;

    public $realValue;
    
    public $actionValue;

    /**
     * Set real value
     *
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        $this->type = static::TYPE_VALUE;
        $this->realValue = $value;

        return $this;
    }

    /**
     * Set action mode
     *
     * @param $action
     * @return $this
     */
    public function action($action)
    {
        $this->type = static::TYPE_ACTION;
        $this->actionValue = $action;

        return $this;
    }


    public bool $enabled = true;

    /**
     * Enable when condition
     *
     * @param $condition
     * @return $this
     */
    public function when($condition)
    {
        $this->enabled = (bool) value($condition);
        return $this;
    }

    /**
     * Enable unless condition
     *
     * @param $condition
     * @return $this
     */
    public function unless($condition)
    {
        $this->enabled = !value($condition);
        return $this;
    }

}
