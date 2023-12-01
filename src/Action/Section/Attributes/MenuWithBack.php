<?php

namespace Mmb\Laravel\Action\Section\Attributes;

use Attribute;
use Mmb\Laravel\Action\Section\Menu;

#[Attribute(Attribute::TARGET_METHOD)]
class MenuWithBack extends MenuAttribute
{

    public function __construct(
        public $action = 'back',
    )
    {
    }

    public function after(Menu $menu)
    {
        $menu->footer([
            [ $menu->key(__('menu.key.back'), $this->action) ],
        ]);
    }

}
