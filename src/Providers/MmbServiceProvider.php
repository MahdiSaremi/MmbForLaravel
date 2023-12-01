<?php

namespace Mmb\Laravel\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandlerBase;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mmb\Laravel\Action\Filter\Filter;
use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Http\Controllers\WebhookController;
use Mmb\Laravel\Core\Requests\Parser\ArgsParserFactory;
use Mmb\Laravel\Core\Requests\Parser\DefaultArgsParser;
use Mmb\Laravel\Core\Bot;
use Mmb\Laravel\Support\Exceptions\CallableException;
use NunoMaduro\Collision\Adapters\Laravel\ExceptionHandler as NunoExceptionHandler;
use PhpParser\Node\Expr\Closure;
use Throwable;

class MmbServiceProvider extends ServiceProvider
{

    /**
     * Robot webhook route
     *
     * @var string
     */
    protected $route = '/bot/{token}';

    /**
     * Update handlers
     *
     * @var array
     */
    protected $handlers = [];

    public final function register()
    {
        $this->app->singleton(Bot::class, fn() => $this->createBot());
        $this->app->bind(ArgsParserFactory::class, DefaultArgsParser::class);

        $this->booted(function()
        {
            if(!($this->app instanceof CachesRoutes && $this->app->routesAreCached()))
            {
                $this->registerRoutes();
            }
        });
    }

    /**
     * Register routes
     *
     * @return void
     */
    public function registerRoutes()
    {
        Route::post($this->route, [WebhookController::class, 'update']);
    }

    /**
     * Create bot instance and initialize it
     *
     * @return Bot
     */
    public final function createBot() : Bot
    {
        $bot = $this->bot();
        $this->registerBot($bot);

        return $bot;
    }

    /**
     * Register bot
     *
     * @param Bot $bot
     * @return void
     */
    public function registerBot(Bot $bot)
    {
        $bot->registerHandlers($this->handlers);
    }

    /**
     * Get bot instance
     *
     * @return Bot
     */
    public function bot() : Bot
    {
        return new Bot(env('BOT_TOKEN'));
    }

    /**
     * Register filter fail handler
     *
     * @param string|Closure $handler
     * @return void
     */
    public function registerFailHandler(string|Closure $handler)
    {
        Filter::registerFailHandler($handler);
    }

}
