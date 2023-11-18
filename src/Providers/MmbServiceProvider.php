<?php

namespace Mmb\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Mmb\Laravel\Core\Bot;

class MmbServiceProvider extends ServiceProvider
{

    public final function register()
    {
        $this->app->singleton(Bot::class, fn() => $this->bot());
    }

    public function bot()
    {
        return new Bot();
    }

}