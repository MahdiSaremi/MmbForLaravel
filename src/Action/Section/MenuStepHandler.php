<?php

namespace Mmb\Laravel\Action\Section;

use Mmb\Laravel\Action\Action;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerAlias as Alias;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerSafeClass as SafeClass;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerSerialize as Serialize;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerShortClass as ShortClass;
use Mmb\Laravel\Action\Memory\Attributes\StepHandlerArray as AsArray;
use Mmb\Laravel\Action\Memory\StepHandler;
use Mmb\Laravel\Core\Updates\Update;

class MenuStepHandler extends StepHandler
{

    #[Alias('C')]
    #[SafeClass]
    public $initalizeClass;

    #[Alias('M')]
    public $initalizeMethod;

    #[Alias('a')]
    #[AsArray]
    public $actionMap;

    #[Alias('d')]
    #[Serialize]
    public $withinData;

    public function fromMenu(Menu $menu)
    {
        $menu->makeReady();
        [$this->initalizeClass, $this->initalizeMethod] = $menu->getInitializer();
        $this->withinData = $menu->cachedWithinData ?: null;
        $this->actionMap = $menu->cachedStorableActions ?: null;

        return $this;
    }

    public function toMenu(Update $update = null)
    {
        $menu = new Menu($update);
        $menu->isCreating = false;
        $menu->setStoredWithData($this->withinData ?: []);

        if(
            $this->initalizeClass &&
            is_a($this->initalizeClass, Action::class, true) &&
            method_exists($this->initalizeClass, $this->initalizeMethod)
        )
        {
            $this->initalizeClass::initializeMenuOf($this->initalizeMethod, $menu, $update);
        }

        $menu->makeReadyFromStore($this->actionMap ?: []);

        return $menu;
    }

    public function handle(Update $update)
    {
        $menu = $this->toMenu($update);
        $ok = $menu->fire($update);

        if(!$ok)
        {
            $update->skipHandler();
        }
    }

}
