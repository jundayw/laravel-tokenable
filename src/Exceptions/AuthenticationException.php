<?php

namespace Jundayw\Tokenable\Exceptions;

use Throwable;

class AuthenticationException extends TokenableException
{
    public function __construct(string $message = 'Unauthenticated', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
