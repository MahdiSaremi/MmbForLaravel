<?php

namespace Mmb\Laravel\Action;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Mmb\Laravel\Action\Section\Attributes\MenuAttribute;
use Mmb\Laravel\Action\Section\Menu;
use Mmb\Laravel\Core\Bot;
use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Support\AttributeLoader\HasAttributeLoader;
use Mmb\Laravel\Support\Caller\Caller;
use Mmb\Laravel\Support\Caller\StatusHandleBackException;
use Mmb\Laravel\Support\Db\Attributes\FindBy;
use Mmb\Laravel\Support\Db\ModelFinder;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class Action
{
    use HasAttributeLoader;

    public Update $update;

    public function __construct(
        Update $update = null,
    )
    {
        $this->update = $update ?? app(Update::class);
        $this->boot();
    }

    /**
     * Boot section
     *
     * @return void
     */
    protected function boot()
    {
    }

    /**
     * Invoke a method
     *
     * @param string $method
     * @param        ...$args
     * @return mixed
     */
    public function invoke(string $method, ...$args)
    {
        try
        {
            return Caller::invoke([$this, $method], $args, $this->getInvokeDynamicParameters($method));
        }
        catch(HttpException $exception)
        {
            if(!($exception instanceof StatusHandleBackException) &&
                method_exists($this, $fn = 'error' . $exception->getStatusCode()))
            {
                throw new StatusHandleBackException(
                    [$this, $fn], $exception->getStatusCode(), $exception->getMessage(), $exception,
                    $exception->getHeaders(), $exception->getCode()
                );
            }

            throw $exception;
        }
    }

    /**
     * Invoke a method with dynamic parameters
     *
     * @param string $method
     * @param array  $normalArgs
     * @param array  $dynamicArgs
     * @return mixed
     */
    public function invokeDynamic(string $method, array $normalArgs, array $dynamicArgs)
    {
        try
        {
            return Caller::invoke(
                [$this, $method], $normalArgs, $dynamicArgs + $this->getInvokeDynamicParameters($method)
            );
        }
        catch(HttpException $exception)
        {
            if(!($exception instanceof StatusHandleBackException) &&
                method_exists($this, $fn = 'error' . $exception->getStatusCode()))
            {
                throw new StatusHandleBackException(
                    [$this, $fn], $exception->getStatusCode(), $exception->getMessage(), $exception,
                    $exception->getHeaders(), $exception->getCode()
                );
            }

            throw $exception;
        }
    }

    /**
     * Load menu from
     *
     * @param string    $name
     * @param Menu|null $menu
     * @param array     $args
     * @return Menu
     */
    public function initializeMenu(string $name, Menu $menu = null, array $args = [])
    {
        $menu ??= new Menu($this->update);
        $menu->initializer($this, $name);

        $attrs = static::getMethodAttributesOf($name, MenuAttribute::class);
        foreach($attrs as $attr)
        {
            $attr->before($menu);
        }

        if($menu->isCreating())
        {
            $args = static::getNormalizedCallingMethod(
                $name, $args,
                fn(\ReflectionParameter $parameter, $value) => $menu->have(
                    $parameter->getName(),
                    $_,
                    $value
                ),
            );
        }
        elseif($menu->isLoading())
        {
            $parameters = (new \ReflectionMethod($this, $name))->getParameters();
            foreach($parameters as $parameter)
            {
                if($parameter->getName() == 'menu')
                    continue;

                $menu->have($parameter->getName(), $argument);
                $args[$parameter->getName()] = $argument;
            }
        }

        foreach($attrs as $attr)
        {
            $attr->modifyArgs($menu, $args);
        }

        $this->invokeDynamic(
            $name, $args, [
                'menu' => $menu,
            ]
        );

        foreach($attrs as $attr)
        {
            $attr->after($menu);
        }

        return $menu;
    }

    /**
     * Make menu
     *
     * @param string      $name
     * @param Menu        $menu
     * @param Update|null $update
     * @return Menu
     */
    public static function initializeMenuOf(string $name, Menu $menu, Update $update = null)
    {
        $instance = new static($update);
        return $instance->initializeMenu($name, $menu);
    }

    /**
     * Get dynamic parameters when invoke a method
     *
     * @param string $method
     * @return array
     */
    protected function getInvokeDynamicParameters(string $method)
    {
        return [];
    }

    /**
     * Get bot
     *
     * @return Bot
     */
    public function bot()
    {
        return $this->update?->bot() ?? app(Bot::class);
    }

    /**
     * Response to update message
     *
     * @param       $message
     * @param array $args
     * @return mixed
     */
    public function response($message, array $args = [])
    {
        return $this->update->response($message, $args);
    }

    /**
     * Tell message to update callback / other
     *
     * @param       $message
     * @param array $args
     * @return void
     */
    public function tell($message, array $args = [])
    {
        return $this->update->tell($message, $args);
    }

}
