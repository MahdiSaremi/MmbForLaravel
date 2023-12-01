<?php

namespace Mmb\Laravel\Action\Form;

use Mmb\Laravel\Action\Action;
use Mmb\Laravel\Action\Filter\FilterFailException;
use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Support\Caller\HasSimpleEvents;

class Form extends Action
{
    use HasSimpleEvents;

    protected $inputs = null;

    protected $path = null;

    public function inputs()
    {
        return $this->inputs ?? $this->path;
    }

    private array $_cached_inputs;
    private array $_cached_path;

    /**
     * Get all inputs (value will cache)
     *
     * @return array
     */
    public function getInputs()
    {
        if(!isset($this->_cached_inputs))
        {
            $this->_cached_inputs = [];
            foreach($this->inputs() as $key => $value)
            {
                if(is_int($key))
                {
                    $key = $value;
                    $value = method_exists($this, $key) ? $this->$key(...) : null;
                }

                $this->_cached_inputs[$key] = $value;
            }
        }

        return $this->_cached_inputs;
    }

    /**
     * Get path inputs name
     *
     * @return string[]
     */
    public function getPath()
    {
        return $this->_cached_path ??= array_keys($this->getInputs());
    }

    /**
     * Request form
     *
     * @param array       $attributes
     * @param Update|null $update
     * @return void
     */
    public static function request(array $attributes = [], Update $update = null)
    {
        $form = new static($update);
        $form->attributes = $attributes;
        $form->startForm();
    }


    private array $attributes = [];

    /**
     * Get attribute
     *
     * @param string $name
     * @param        $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->has($name) ? $this->attributes[$name] : value($default);
    }

    /**
     * Set attribute value
     *
     * @param string $name
     * @param        $value
     * @return void
     */
    public function put(string $name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Checks have attribute
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Get attribute
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Set attribute
     *
     * @param string $name
     * @param        $value
     * @return void
     */
    public function __set(string $name, $value) : void
    {
        $this->put($name, $value);
    }


    /**
     * Make empty input
     *
     * @param string $name
     * @return Input
     */
    public function emptyInput(string $name)
    {
        if($parameter = @(new \ReflectionMethod($this, $name))->getParameters()[0])
        {
            $type = $parameter->getType();
            if($type instanceof \ReflectionNamedType)
            {
                $class = $type->getName();
                if(is_a($class, Input::class, true))
                {
                    return new $class($this, $name);
                }
            }
        }

        return new Input($this, $name);
    }

    /**
     * Create input (requesting)
     *
     * @param string $name
     * @return Input
     */
    public function createInput(string $name)
    {
        $input = $this->emptyInput($name);
        $input->isCreatingMode = true;
        $this->fire('initializingInput', $input);
        $this->invokeDynamic(
            $name, [], [
                'input' => $input,
                'form'  => $this,
            ]
        );
        $this->fire('initializedInput', $input);

        return $input;
    }

    /**
     * Loading input (filling)
     *
     * @param string $name
     * @return Input
     */
    public function loadInput(string $name)
    {
        $input = $this->emptyInput($name);
        $input->isCreatingMode = false;
        $this->fire('initializingInput', $input);
        $this->invokeDynamic(
            $name, [], [
                'input' => $input,
                'form'  => $this,
            ]
        );
        $this->fire('initializedInput', $input);

        return $input;
    }


    public ?string $currentInput = null;

    public function startForm()
    {
        $this->handleBy(
            function()
            {
                $this->fire('start');
                $this->first();
            }
        );
    }

    public function continueForm()
    {
        $this->handleBy(
            function()
            {
                $this->fire('step');
                $this->pass();
                $this->next();
            }
        );
    }

    public function handleBy($callback)
    {
        try
        {
            $callback();
            $this->storeStepHandler();
        }
        catch(FilterFailException $failException)
        {
            return null;
        }
        catch(ForceActionFormException $forceAction)
        {
            if($forceAction->store)
            {
                $this->storeStepHandler();
            }

            return null;
        }
    }

    public function stop(bool $store = false)
    {
        throw new ForceActionFormException($store);
    }

    public function goto(string $name)
    {
        $input = $this->createInput($name);
        $this->currentInput = $name;

        $this->fire('enter', $input);
        $input->request();

        $this->stop(true);
    }

    public function finish()
    {
        $this->fire('finish');
        $this->stop();
    }

    public function next()
    {
        if($next = $this->findNextInput($this->currentInput))
        {
            $this->goto($next);
        }

        $this->finish();
    }

    public function before()
    {
        if($next = $this->findNearBeforeInput($this->currentInput))
        {
            $this->goto($next);
        }

        $this->first();
    }

    public function first()
    {
        if($first = @$this->getPath()[0])
        {
            $this->goto($first);
        }

        $this->finish();
    }

    /**
     * Reset and restart form requesting
     *
     * @return void
     */
    public function restart()
    {
        $this->reset();
        $this->startForm();
        $this->stop();
    }

    /**
     * Forget all attributes
     *
     * @return void
     */
    public function reset()
    {
        $this->attributes = [];
    }

    public function error(string|FilterFailException $message)
    {
        if($message instanceof FilterFailException)
        {
            $message = $this->formatFilterError($message);
        }

        $this->fire('error', $message);
        $this->stop();
    }

    /**
     * Store step handler
     *
     * @return void
     */
    public function storeStepHandler()
    {
        $stepHandler = FormStepHandler::make();
        $stepHandler->attributes = $this->attributes ?: null;
        $stepHandler->currentInput = $this->currentInput;
        $stepHandler->class = static::class;
        $stepHandler->keep();
    }

    /**
     * Load step handler
     *
     * @param FormStepHandler $stepHandler
     * @return void
     */
    public function loadStepHandler(FormStepHandler $stepHandler)
    {
        $this->attributes = $stepHandler->attributes ?? [];
        $this->currentInput = $stepHandler->currentInput;
    }

    /**
     * Pass update to input
     *
     * @param string|null $name
     * @param Update|null $update
     * @return void
     */
    public function pass(?string $name = null, ?Update $update = null)
    {
        $input = $this->loadInput($name ?? $this->currentInput);

        $input->pass($update ?? $this->update);
        $this->fire('leave', $input);
    }

    public function findInputIndex(string $name)
    {
        $index = array_search($name, $this->getPath());
        return $index === false ? -1 : $index;
    }

    public function findNextInput(string $name)
    {
        $index = $this->findInputIndex($name);

        if($index === -1)
        {
            return $this->findNearNextInput($name);
        }

        return $this->getPath()[$index + 1] ?? false;
    }

    public function findNearNextInput(string $name)
    {
        $passed = false;
        foreach($this->getInputs() as $key => $ignored)
        {
            if($key == $name)
            {
                $passed = true;
            }
            elseif($passed && $this->findInputIndex($key) != -1)
            {
                return $key;
            }
        }

        return false;
    }

    public function findNearBeforeInput(string $name)
    {
        $may = false;
        foreach($this->getInputs() as $key => $ignored)
        {
            if($key == $name)
            {
                break;
            }
            elseif($this->findInputIndex($key) != -1)
            {
                $may = $key;
            }
        }

        return $may;
    }


    /**
     * Format filter exception message
     *
     * @param FilterFailException $failException
     * @return string
     */
    public function formatFilterError(FilterFailException $failException)
    {
        return $failException->description;
    }

    /**
     * Event on input initializing
     *
     * @param Input $input
     * @return void
     */
    public function onInitializingInput(Input $input)
    {
    }

    /**
     * Event on input initialized
     *
     * @param Input $input
     * @return void
     */
    public function onInitializedInput(Input $input)
    {
    }

    /**
     * Event on error occurred
     *
     * @param string $message
     * @return void
     */
    public function onError(string $message)
    {
        $this->update->response($message);
    }

    /**
     * Event on starting the form
     *
     * @return void
     */
    public function onStart()
    {
    }

    /**
     * Event on stepping the form
     *
     * @return void
     */
    public function onStep()
    {
    }

    /**
     * Event on entering input
     *
     * @param Input $input
     * @return void
     */
    public function onEnter(Input $input)
    {
    }

    /**
     * Event on leaving input
     *
     * @param Input $input
     * @return void
     */
    public function onLeave(Input $input)
    {
    }

    /**
     * Event on finish
     *
     * @return void
     */
    public function onFinish()
    {
    }

    /**
     * Event on request input
     *
     * @param Input $input
     * @param       $message
     * @return void
     */
    public function onRequest(Input $input, $message)
    {
        $this->response(
            $message + [
                'key' => $input->getKeyBuilder()->toArray(),
            ]
        );
    }

}
