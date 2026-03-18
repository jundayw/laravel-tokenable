<?php

namespace Jundayw\Tokenable\Exceptions;

use RuntimeException;
use Throwable;

class TokenableException extends RuntimeException
{
    public function __construct(string $message = "Tokenable Exception", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
