<?php

use Mmb\Laravel\Core\Bot;
use Mmb\Laravel\Core\Updates\Callbacks\Callback;
use Mmb\Laravel\Core\Updates\Messages\Message;
use Mmb\Laravel\Core\Updates\Update;

if(!function_exists('bot'))
{
    function bot() : ?Bot
    {
        return app(Bot::class);
    }
}

if(!function_exists('upd'))
{
    function upd() : ?Update
    {
        return app(Update::class);
    }
}

if(!function_exists('msg'))
{
    function msg() : ?Message
    {
        return upd()?->getMessage();
    }
}

if(!function_exists('callback'))
{
    function callback() : ?Callback
    {
        return upd()?->callbackQuery;
    }
}

if(!function_exists('smartTypeOf'))
{
    function smartTypeOf($value) : string
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }
}
