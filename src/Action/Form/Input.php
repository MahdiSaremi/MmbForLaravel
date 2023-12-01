<?php

namespace Mmb\Laravel\Action\Form;

use Closure;
use Mmb\Laravel\Action\Filter\Filterable;
use Mmb\Laravel\Action\Filter\FilterableShort;
use Mmb\Laravel\Action\Filter\FilterFailException;
use Mmb\Laravel\Action\Filter\HasEventFilter;
use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Support\Caller\HasSimpleEvents;

/**
 * @property mixed $value
 */
class Input
{
    use Filterable, FilterableShort, HasEventFilter, HasSimpleEvents;

    public bool $isCreatingMode = false;

    public function __construct(
        public Form   $form,
        public string $name,
    )
    {
    }

    public function isCreating()
    {
        return $this->isCreatingMode;
    }

    public function isLoading()
    {
        return !$this->isCreatingMode;
    }


    public $askValue;

    /**
     * Set request message
     *
     * @param $message
     * @return $this
     */
    public function ask($message)
    {
        $this->askValue = $message;
        return $this;
    }

    public ?string $placeholderValue = null;

    /**
     * Set placeholder message
     *
     * @param string $message
     * @return $this
     */
    public function placeholder(string $message)
    {
        $this->placeholderValue = $message;
        return $this;
    }


    /**
     * Pass update
     *
     * @param Update $update
     * @return void
     */
    public function pass(Update $update)
    {
        $this->value = $this->passFilter($update)[2];
        $this->fire('pass');
    }

    /**
     * Request input
     *
     * @param mixed $message
     * @return void
     */
    public function request($message = null)
    {
        $message = value($message ?? $this->askValue);
        if(is_string($message))
        {
            $message = ['text' => $message];
        }

        $this->fire('request', $message);
    }

    /**
     * Default fail catching
     *
     * @param FilterFailException $e
     * @param Update              $update
     * @return void
     */
    protected function defaultFailCatch(FilterFailException $e, Update $update)
    {
        $this->form->error($e);
    }

    /**
     * Event on request
     *
     * @param $message
     * @return void
     */
    public function onRequest($message)
    {
        $this->form->fire('request', $this, $message);
    }

    /**
     * Get magic properties
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if($name == 'value')
        {
            return $this->form->get($this->name);
        }

        error_log(sprintf("Undefined property [%s] on [%s]", $name, static::class));
        return null;
    }

    /**
     * Set magic properties
     *
     * @param string $name
     * @param        $value
     * @return void
     */
    public function __set(string $name, $value) : void
    {
        if($name == 'value')
        {
            $this->form->put($this->name, $value);
            return;
        }

        $this->$name = $value;
    }


    /**
     * Make new form key
     *
     * @param string $text
     * @param        $value
     * @return FormKey
     */
    public function key(string $text, $value = null)
    {
        return FormKey::make(...func_get_args());
    }

    /**
     * Make new form key with action
     *
     * @param string $text
     * @param        $action
     * @return FormKey
     */
    public function keyAction(string $text, $action)
    {
        return FormKey::makeAction($text, $action);
    }


    private array $keyBuilderQueue = [];
    private FormKeyBuilder $keyBuilder;

    /**
     * Add a job to key builder queue
     *
     * @param bool   $fixed
     * @param string $method
     * @param        ...$args
     * @return void
     */
    protected function addKeyBuilderQueue(bool $fixed, string $method, ...$args)
    {
        if(isset($this->keyBuilder))
        {
            $this->keyBuilder->$method(...$args);
        }
        else
        {
            $this->keyBuilderQueue[] = [$fixed, $method, $args];
        }
    }

    /**
     * Get key builder
     *
     * @return FormKeyBuilder
     */
    public function getKeyBuilder()
    {
        if(!isset($this->keyBuilder))
        {
            $this->keyBuilder = new FormKeyBuilder($this->form);

            foreach($this->keyBuilderQueue as $queue)
            {
                [$fixed, $method, $args] = $queue;
                $this->keyBuilder->$method(...$args);
            }

            $this->keyBuilderQueue = [];
        }

        return $this->keyBuilder;
    }

    /**
     * Add key options
     *
     * @param array|Closure $options
     * @param bool          $fixed
     * @return $this
     */
    public function options(array|Closure $options, bool $fixed = false)
    {
        $this->addKeyBuilderQueue($fixed, 'scheme', $options);
        return $this;
    }

    /**
     * Add key options (fixed)
     *
     * @param array|Closure $options
     * @return $this
     */
    public function optionsFixed(array|Closure $options)
    {
        return $this->options($options, true);
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
        $this->addKeyBuilderQueue(true, 'add', ...func_get_args());
        return $this;
    }

    /**
     * Add key to last line
     *
     * @param array|FormKey|string $key
     * @param                      $value
     * @return $this
     */
    public function push(array|FormKey|string $key, $value = null)
    {
        $this->addKeyBuilderQueue(true, 'push', ...func_get_args());
        return $this;
    }

    /**
     * Add empty key row
     *
     * @return $this
     */
    public function break()
    {
        $this->addKeyBuilderQueue(true, 'break');
        return $this;
    }

}
