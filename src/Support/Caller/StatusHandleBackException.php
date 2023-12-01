<?php

namespace Mmb\Laravel\Support\Caller;

use Mmb\Laravel\Core\Updates\Update;
use Mmb\Laravel\Support\Exceptions\CallableException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StatusHandleBackException extends HttpException implements CallableException
{

    public function __construct(
        public $callback,
        int $statusCode, string $message = '', \Throwable $previous = null, array $headers = [], int $code = 0
    )
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public function invoke(Update $update)
    {
        Caller::invoke($this->callback, [], [
            'update' => $update,
            'code' => $this->getStatusCode(),
            'exception' => $this,
        ]);
    }

}
