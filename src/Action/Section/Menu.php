<?php

namespace Mmb\Laravel\Action\Section;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use Mmb\Laravel\Action\Action;
use Mmb\Laravel\Action\Filter\Filter;
use Mmb\Laravel\Action\Filter\Filterable;
use Mmb\Laravel\Action\Filter\FilterFailException;
use Mmb\Laravel\Action\Filter\FilterRule;
use Mmb\Laravel\Action\Filter\HasEventFilter;
use Mmb\Laravel\Action\Memory\ConvertableToStep;
use Mmb\Laravel\Action\Memory\Step;
use Mmb\Laravel\Action\Memory\StepHandler;
use Mmb\Laravel\Core\Updates\Messages\Message;
use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Support\Action\ActionCallback;
use Mmb\Laravel\Support\Caller\Caller;
use Mmb\Laravel\Support\Db\ModelFinder;

class Menu implements ConvertableToStep
{
    use Macroable, Filterable, HasEventFilter;

    public Update $update;

    public function __construct(
        Update $update = null,
    )
    {
        $this->update = $update ?? app(Update::class);
    }

    protected         $initializerClass  = null;
    protected ?string $initializerMethod = null;

    /**
     * Set initializer method to reload with next update
     *
     * @param mixed  $object
     * @param string $method
     * @return $this
     */
    public function initializer($object, string $method)
    {
        if(!is_a($object, Action::class, true))
        {
            throw new \TypeError(
                sprintf(
                    "Initializer object must be instance of [%s], given [%s]",
                    Action::class,
                    is_string($object) ? $object : smartTypeOf($object)
                )
            );
        }

        $this->initializerClass = $object;
        $this->initializerMethod = $method;

        return $this;
    }

    protected array $keyInitializer = [];

    protected array $keyHeader = [];

    protected array $keyFooter = [];

    /**
     * Set/Add menu key
     *
     * If store() is enabled, this values will save to user table and load with next update.
     * Otherwise, this values is not saving and menu key will reload from your codes.
     *
     * @param array|Closure $key
     * @param string        $name
     * @param bool          $fixed
     * @param bool          $exclude
     * @return $this
     */
    public function schema(array|Closure $key, string $name = 'main', bool $fixed = false, bool $exclude = false)
    {
        $this->keyInitializer[] = new MenuKeyGroup($this, $key, $name, $fixed, $exclude);
        return $this;
    }

    /**
     * Set/Add menu fixed key
     *
     * @param array|Closure $key
     * @param string        $name
     * @return $this
     */
    public function schemaFixed(array|Closure $key, string $name = 'main')
    {
        return $this->schema($key, $name, true);
    }

    /**
     * Set/Add menu that not included in loading menu
     *
     * @param array|Closure $key
     * @param string        $name
     * @return $this
     */
    public function schemaExcluded(array|Closure $key, string $name = 'main')
    {
        return $this->schema($key, $name, exclude: true);
    }

    /**
     * Set menu header key
     *
     * This values always load from your codes.
     *
     * @param array|Closure $key
     * @param string        $name
     * @param bool          $exclude
     * @return $this
     */
    public function header(array|Closure $key, string $name = 'main', bool $exclude = false)
    {
        $this->keyHeader[] = new MenuKeyGroup($this, $key, $name, true, $exclude);
        return $this;
    }

    /**
     * Set menu footer key
     *
     * This values always load from your codes.
     *
     * @param array|Closure $key
     * @param string        $name
     * @param bool          $exclude
     * @return $this
     */
    public function footer(array|Closure $key, string $name = 'main', bool $exclude = false)
    {
        $this->keyFooter[] = new MenuKeyGroup($this, $key, $name, true, $exclude);
        return $this;
    }

    /**
     * Create a key
     *
     * @param       $text
     * @param null  $action
     * @param mixed ...$args
     * @return MenuKey
     */
    public function key($text, $action = null, ...$args)
    {
        return new MenuKey($this, $text, $action, $args);
    }

    protected array $ifScopes = [];

    public function hasMoreIfScope(string $name)
    {
        return !isset($this->ifScopes[$name]);
    }

    public function setIfScope(string $name)
    {
        $this->ifScopes[$name] = true;
    }

    public function removeIfScope(string $name)
    {
        unset($this->ifScopes[$name]);
    }

    /**
     * Loop each of items
     *
     * @template T
     * @param \Traversable<mixed,T>  $items
     * @param Closure(T $item): void $callback
     * @return $this
     */
    public function foreach(iterable $items, Closure $callback)
    {
        foreach($items as $item)
        {
            $callback($item);
        }

        return $this;
    }

    /**
     * Loop each of items when loading
     *
     * @template T
     * @param Closure|\Traversable<mixed,T> $items
     * @param Closure(T $item): void        $callback
     * @return $this
     */
    public function foreachLoading(Closure|iterable $items, Closure $callback)
    {
        return $this->loading(fn() => $this->foreach(value($items), $callback));
    }


    public bool $isCreating = true;

    /**
     * Checks is creating mode
     *
     * @return bool
     */
    public function isCreating()
    {
        return $this->isCreating;
    }

    /**
     * Checks is loading mode
     *
     * @return bool
     */
    public function isLoading()
    {
        return !$this->isCreating;
    }

    /**
     * Fire callback on creating menu (not invoke when user clicks)
     *
     * @param Closure(Menu $menu): mixed $callback
     * @return $this
     */
    public function creating(Closure $callback)
    {
        if($this->isCreating())
        {
            $callback($this);
        }

        return $this;
    }

    /**
     * Fire callback on loading menu (when user clicks)
     *
     * @param Closure(Menu $menu): mixed $callback
     * @return $this
     */
    public function loading(Closure $callback)
    {
        if($this->isLoading())
        {
            $callback($this);
        }

        return $this;
    }


    protected bool $store = false;

    /**
     * Enable storing mode
     *
     * @return $this
     */
    public function store()
    {
        $this->store = true;
        return $this;
    }

    protected array $storedWithData;
    protected array $withs = [];

    /**
     * With properties
     *
     * If menu is loading, load properties from stored data
     *
     * @param string ...$names
     * @return $this
     */
    public function with(string ...$names)
    {
        array_push($this->withs, ...$names);

        if($this->isLoading() && isset($this->storedWithData) && $this->initializerClass)
        {
            foreach($names as $name)
            {
                if(array_key_exists($name, $this->storedWithData))
                {
                    $this->initializerClass->$name = $this->storedWithData[$name];
                }
            }
        }

        return $this;
    }

    protected array $haveData = [];

    /**
     * Save or reload data from menu data
     *
     * @param string $name
     * @param        $value
     * @param        $default
     * @return $this
     */
    public function have(string $name, &$value, $default = null)
    {
        if($this->isCreating())
        {
            if(count(func_get_args()) > 2)
            {
                $value = value($default);
            }

            $this->haveData[$name] = $value;
        }
        elseif($this->isLoading())
        {
            $value = $this->storedWithData[$name];
        }

        return $this;
    }

    /**
     * Save or reload model data from menu data
     *
     * This function will save model key only
     *
     * @param string     $name
     * @param string     $class
     * @param Model|null $value
     * @param            $default
     * @return $this
     */
    public function haveModel(string $name, string $class, ?Model &$value, $default = null)
    {
        if($this->isCreating())
        {
            if(count(func_get_args()) > 2)
            {
                $value = ModelFinder::findOrFail($class, value($default));
            }
            elseif($value)
            {
                $value = $value->{$value->getKey()};
            }

            $this->haveData[$name] = $value;
        }
        elseif($this->isLoading())
        {
            $value = ModelFinder::findOrFail($class, $this->storedWithData[$name]);
        }

        return $this;
    }

    protected array $onActions = [];

    /**
     * Fire action when another action invoked
     *
     * @param string|array|FilterRule $actionName
     * @param mixed                   $action
     * @return $this
     */
    public function on(string|array|FilterRule $actionName, $action = null)
    {
        if($actionName instanceof FilterRule)
        {
            $this->addFilterEvent($actionName, new ActionCallback($action));
            return $this;
        }

        if(is_array($actionName))
        {
            foreach($actionName as $name => $value)
            {
                $this->on($name, $value);
            }
        }
        else
        {
            $this->onActions[$actionName] = new ActionCallback($action);
        }

        return $this;
    }

    /**
     * Add event for regex pattern text message
     *
     * @param string $pattern
     * @param        $action
     * @param int    $pass
     * @return $this
     */
    public function onRegex(string $pattern, $action, int $pass = -2)
    {
        return $this->on(
            Filter::make()->regex($pattern, $pass, ''),
            $action
        );
    }

    protected $else = null;

    /**
     * Fire action when user send another messages
     *
     * @param $action
     * @return $this
     */
    public function else($action)
    {
        $this->else = new ActionCallback($action);
        return $this;
    }

    /**
     * Fire else updates
     *
     * @param Update $update
     * @return bool
     */
    public function fireElse(Update $update)
    {
        // Find filter events
        if($this->getMatchedFilter($update, $action, $value))
        {
            $this->fireAction($action, $update, [$value]);
            return true;
        }

        // Else action
        if(isset($this->else))
        {
            [$ok, $passed, $value] = $this->passFilter($update);
            if(!$ok)
            {
                return $passed;
            }

            $this->fireAction($this->else, $update, $this->passFilterResult ? [$value] : []);
            return true;
        }

        return false;
    }

    /**
     * Send menu as message
     *
     * @param       $message
     * @param array $args
     * @param       ...$namedArgs
     * @return Message|null
     */
    public function send($message = null, array $args = [], ...$namedArgs)
    {
        $this->makeReady();

        return tap(
            $this->update->getChat()->sendMessage($message, $args + $namedArgs + ['key' => $this->cachedKey]),
            function()
            {
                Step::set($this);
            }
        );
    }

    /**
     * Reply menu as message
     *
     * @param       $message
     * @param array $args
     * @param       ...$namedArgs
     * @return Message|null
     */
    public function reply($message = null, array $args = [], ...$namedArgs)
    {
        $this->makeReady();

        return tap(
            $this->update->getMessage()->replyMessage($message, $args + $namedArgs + ['key' => $this->cachedKey]),
            function()
            {
                Step::set($this);
            }
        );
    }


    protected bool $isReady               = false;
    public ?array  $cachedKey             = null;
    public ?array  $cachedActions         = null;
    public ?array  $cachedStorableActions = null;
    public ?array  $cachedWithinData      = null;

    /**
     * Load and cache keys and other data
     *
     * @return void
     */
    public function makeReady()
    {
        if($this->isReady)
        {
            return;
        }

        $this->makeReadyKey();
        $this->makeReadyWithinData();

        $this->isReady = true;
    }

    private function makeReadyKey(array $storeActions = null)
    {
        $this->cachedKey = [];
        $this->cachedActions = [];
        $this->cachedStorableActions = [];

        if($storeActions !== null)
        {
            $this->cachedActions = array_replace($this->cachedActions, $storeActions);
        }

        $this->makeReadyKeyGroup($this->keyHeader, false);
        $this->makeReadyKeyGroup($this->keyInitializer, $this->store, $storeActions !== null);
        $this->makeReadyKeyGroup($this->keyFooter, false);
    }

    private function makeReadyKeyGroup(array $group, bool $store = true, bool $skipStorable = false)
    {
        /** @var MenuKeyGroup $keyGroup */
        foreach($group as $keyGroup)
        {
            $storable = $store && !$keyGroup->fixed && !$keyGroup->exclude;

            // If storable, skip
            if($storable && $skipStorable)
            {
                continue;
            }

            // Loading mode & Excluded groups
            if($this->isLoading() && $keyGroup->exclude)
            {
                continue;
            }

            // Convert key items group to key array and actions
            [$key, $actions] = $keyGroup->normalizeKey($storable);

            // Save key and actions
            array_push($this->cachedKey, ...$key);
            $this->cachedActions = array_replace($this->cachedActions, $actions);

            // Save storable actions
            if($storable)
            {
                $storableActions = array_map(fn(ActionCallback $action) => $action->toArray(), $actions);
                $this->cachedStorableActions = array_replace($this->cachedStorableActions, $storableActions);
            }
        }
    }

    private function makeReadyWithinData()
    {
        if(is_object($this->initializerClass))
        {
            $this->cachedWithinData = $this->haveData;

            foreach($this->withs as $with)
            {
                $this->cachedWithinData[$with] = $this->initializerClass->$with;
            }
        }
    }

    public function makeReadyFromStore(array $actions)
    {
        if($this->isReady)
        {
            return;
        }

        $actions = array_map(fn($array) => ActionCallback::fromArray($array), $actions);
        $this->makeReadyKey($actions);

        $this->isReady = true;
    }

    /**
     * Set stored 'with' data
     *
     * @param array $with
     * @return void
     */
    public function setStoredWithData(array $with)
    {
        $this->storedWithData = $with;
    }

    /**
     * Get initializer
     *
     * @return array
     */
    public function getInitializer()
    {
        return [
            is_string($this->initializerClass) ?
                $this->initializerClass :
                get_class($this->initializerClass),
            $this->initializerMethod,
        ];
    }

    /**
     * Find action name from update
     *
     * @param Update $update
     * @return ?ActionCallback
     */
    public function findActionFrom(Update $update)
    {
        $action = null;
        $actionKey = MenuKey::findActionKeyFrom($update);

        if($actionKey !== null)
        {
            $action = $this->cachedActions[$actionKey] ?? null;
        }

        if($action instanceof ActionCallback && $action->isNamed() && array_key_exists(
                $action->action, $this->onActions
            ))
        {
            $action = $this->onActions[$action[0]]->with($action->defaultArgs);
        }

        return $action;
    }

    /**
     * Find action callable
     *
     * @param array  $action
     * @param Update $update
     * @return ?callable
     */
    public function findActionCallable(array $action, Update $update)
    {
        @[$callable, $args] = $action;
        $args ??= [];

        // Closure action: fn() => Something
        if($callable instanceof Closure)
        {
            return [$callable, $args];
        }

        // Array action: [SomeSection::class, 'someMethod']
        elseif(is_array($callable))
        {
            [$class, $method] = $callable;
            $class = $class::make($update);

            return [$class, $method, $args];
        }


        if($this->initializerClass)
        {
            return [$this->initializerClass, $callable, $args];
        }

        return null;
    }

    /**
     * Fire action
     *
     * @param ActionCallback|string $name
     * @param Update                $update
     * @param array                 $args
     * @return void
     */
    public function fireAction(ActionCallback|string $name, Update $update, array $args = [])
    {
        if(is_string($name))
        {
            $name = new ActionCallback($name);
        }

        $name->invoke(
            $this->initializerClass,
            $update,
            $args,
            [
                'sender' => $this,
            ]
        );
    }

    /**
     * Fire action for update
     *
     * @param Update $update
     * @return bool
     */
    public function fire(Update $update)
    {
        $action = $this->findActionFrom($update);

        if($action !== null)
        {
            $this->fireAction($action, $update);
            return true;
        }

        return $this->fireElse($update);
    }


    /**
     * Convert to menu step handler
     *
     * @return StepHandler|null
     */
    public function toStep() : ?StepHandler
    {
        return MenuStepHandler::make()->fromMenu($this);
    }

    /**
     * Get variant
     *
     * @param string $name
     * @param        $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        if($this->isCreating())
        {
            if(in_array($name, $this->withs) && $this->initializerClass)
            {
                return $this->initializerClass->$name;
            }
            elseif(array_key_exists($name, $this->haveData))
            {
                return $this->haveData[$name];
            }
        }
        elseif($this->isLoading())
        {
            if(array_key_exists($name, $this->storedWithData))
            {
                return $this->storedWithData[$name];
            }
        }

        return value($default);
    }

    /**
     * Get data as model
     *
     * @param string $name
     * @param string $class
     * @param        $default
     * @return Model|mixed
     */
    public function getModel(string $name, string $class, $default = null)
    {
        $isDefault = false;
        $id = $this->get($name, function() use(&$isDefault)
        {
            $isDefault = true;
        });

        if($isDefault)
        {
            return value($default);
        }

        return ModelFinder::find($class, $id);
    }

    /**
     * Get variant
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get(
            $name, static function() use ($name)
        {
            error_log("Undefined property [$name]");
            return null;
        }
        );
    }

}
