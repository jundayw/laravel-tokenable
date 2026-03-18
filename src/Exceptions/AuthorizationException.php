<?php

namespace Jundayw\Tokenable\Exceptions;

use Throwable;

class AuthorizationException extends TokenableException
{
    public function __construct(string $message = 'Forbidden', int $code = 403, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
