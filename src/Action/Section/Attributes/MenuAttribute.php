<?php

namespace Mmb\Laravel\Action\Section\Attributes;

use Attribute;
use Mmb\Laravel\Action\Section\Menu;

#[Attribute(Attribute::TARGET_METHOD)]
abstract class MenuAttribute
{

    public function before(Menu $menu)
    {
    }

    public function after(Menu $menu)
    {
    }

    public function modifyArgs(Menu $menu, array &$args)
    {
    }

}
