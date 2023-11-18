<?php

namespace Mmb\Laravel\Core\Requests;

use Mmb\Laravel\Core\Bot;

abstract class RequestApi
{

    public bool $ignore = false;

    public function __construct(
        public Bot $bot,
        protected string $token,
        public string $method,
        public array $args,
    )
    {
    }

    protected abstract function execute();

    public final function request()
    {
        try
        {
            return $this->execute();
        }
        catch(\Throwable $throwable)
        {
            if($this->ignore)
            {
                return false;
            }

            throw $throwable;
        }
    }

}