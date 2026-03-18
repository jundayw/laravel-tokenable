<?php

namespace Jundayw\Tokenable\Exceptions;

use Throwable;

class TokenInvalidException extends AuthenticationException
{
    public function __construct(string $message = 'InvalidToken', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
