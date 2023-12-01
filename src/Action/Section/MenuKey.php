<?php

namespace Mmb\Laravel\Action\Section;

use Closure;
use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Support\Action\ActionCallback;

class MenuKey
{

    protected ?ActionCallback $action = null;

    public function __construct(
        public Menu     $menu,
        protected       $text,
                        $action,
        array           $args = [],
        protected array $attrs = [],
    )
    {
        $this->text = trim($text);
        $this->action($action, ...$args);
    }

    protected bool $isVisible = true;

    /**
     * Set key visibility
     *
     * @param $condition
     * @return $this
     */
    public function visible($condition = true)
    {
        $this->isVisible = (bool) value($condition);
        return $this;
    }

    /**
     * Set key hidden
     *
     * @param $condition
     * @return $this
     */
    public function hidden($condition = true)
    {
        $this->isVisible = !value($condition);
        return $this;
    }

    /**
     * Visible key if condition is true
     *
     * @param        $condition
     * @param string $scope
     * @return $this
     */
    public function visibleIf($condition, string $scope = '_')
    {
        $this->isVisible = false;

        if(value($condition))
        {
            $this->menu->setIfScope($scope);
            $this->isVisible = true;
        }
        else
        {
            $this->menu->removeIfScope($scope);
        }

        return $this;
    }

    /**
     * Visible key if before conditions are false and this condition is true.
     *
     * @param        $condition
     * @param string $scope
     * @return $this
     */
    public function visibleElseif($condition, string $scope = '_')
    {
        $this->isVisible = false;

        if($this->menu->hasMoreIfScope($scope))
        {
            if(value($condition))
            {
                $this->menu->setIfScope($scope);
                $this->isVisible = true;
            }
        }

        return $this;
    }

    /**
     * Visible key if before conditions are false.
     *
     * @param string $scope
     * @return $this
     */
    public function visibleElse(string $scope = '_')
    {
        $this->isVisible = false;

        if($this->menu->hasMoreIfScope($scope))
        {
            $this->menu->setIfScope($scope);
            $this->isVisible = true;
        }

        return $this;
    }

    protected bool $display = true;

    /**
     * Display key / Invoke $then, when condition is true.
     *
     * @param              $condition
     * @param Closure|null $then
     * @param Closure|null $default
     * @return $this
     */
    public function when($condition, Closure $then = null, Closure $default = null)
    {
        if($then === null)
        {
            $this->display = (bool) value($condition);
        }
        else
        {
            if(value($condition))
            {
                $then($this);
            }
            elseif($default !== null)
            {
                $default($this);
            }
        }

        return $this;
    }

    /**
     * Display key / Invoke $then, when condition is false.
     *
     * @param              $condition
     * @param Closure|null $then
     * @param Closure|null $default
     * @return $this
     */
    public function unless($condition, Closure $then = null, Closure $default = null)
    {
        if($then === null)
        {
            $this->display = !value($condition);
        }
        else
        {
            if(!value($condition))
            {
                $then($this);
            }
            elseif($default !== null)
            {
                $default($this);
            }
        }

        return $this;
    }

    /**
     * Display key if condition is true.
     *
     * @param        $condition
     * @param string $scope
     * @return $this
     */
    public function if($condition, string $scope = '_')
    {
        $this->display = false;

        if(value($condition))
        {
            $this->menu->setIfScope($scope);
            $this->display = true;
        }
        else
        {
            $this->menu->removeIfScope($scope);
        }

        return $this;
    }

    /**
     * Display key if before conditions are false and this condition is true.
     *
     * @param        $condition
     * @param string $scope
     * @return $this
     */
    public function elseif($condition, string $scope = '_')
    {
        $this->display = false;

        if($this->menu->hasMoreIfScope($scope))
        {
            if(value($condition))
            {
                $this->menu->setIfScope($scope);
                $this->display = true;
            }
        }

        return $this;
    }

    /**
     * Display key if before conditions are false.
     *
     * @param string $scope
     * @return $this
     */
    public function else(string $scope = '_')
    {
        $this->display = false;

        if($this->menu->hasMoreIfScope($scope))
        {
            $this->menu->setIfScope($scope);
            $this->display = true;
        }

        return $this;
    }

    /**
     * Checks if the key can display in list.
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->display;
    }

    protected bool $isIncluded = true;

    /**
     * Don't associate the key.
     *
     * This option will make faster menu in store mode, but key action will not work as well!
     * You can use this option on store mode, and use {@see Menu::onRegex()} method to find clicked key.
     *
     * @return $this
     */
    public function exclude()
    {
        $this->isIncluded = false;
        return $this;
    }

    /**
     * Checks key is included to associating.
     *
     * @return bool
     */
    public function isIncluded()
    {
        return $this->isIncluded && $this->action !== null;
    }

    /**
     * Set key click action
     *
     * @param $action
     * @param ...$args
     * @return $this
     */
    public function action($action, ...$args)
    {
        $this->action = is_null($action) ? null : (
        $action instanceof ActionCallback ? $action : new ActionCallback($action, $args)
        );
        return $this;
    }


    /**
     * Get action
     *
     * @return ?ActionCallback
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get action key to find match
     *
     * @param bool $isInline
     * @return string
     */
    public function getActionKey(bool $isInline = false)
    {
        return static::actionKeyFor('text', $this->text, $isInline); // TODO: Other types
    }

    /**
     * Get action key for key type
     *
     * @param string $type
     * @param null   $value
     * @param bool   $isInline
     * @return string
     */
    public static function actionKeyFor(string $type, $value = null, bool $isInline = false)
    {
        if($isInline)
        {
            return match ($type)
            {
                'text'  => '#' . $value,
                default => '',
            };
        }
        else
        {
            return match ($type)
            {
                'text'     => '.' . $value,
                'contact'  => 'c',
                'location' => 'l',
            };
        }
    }

    /**
     * Find action key from update
     *
     * @param Update $update
     * @return ?string
     */
    public static function findActionKeyFrom(Update $update)
    {
        if($update->message)
        {
            if($update->message->contact)
            {
                return 'c';
            }
            elseif($update->message->location)
            {
                return 'l';
            }
            else // TODO filter type == 'text'
            {
                return '.' . $update->message->text;
            }
        }

        return null;
    }

    /**
     * Check key is visible
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return [
            'text' => $this->getText(),
        ];
    }

}
