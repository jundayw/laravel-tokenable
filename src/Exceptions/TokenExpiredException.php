<?php

namespace Jundayw\Tokenable\Exceptions;

use Throwable;

class TokenExpiredException extends TokenInvalidException
{
    public function __construct(string $message = 'TokenExpired', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
