<?php

namespace Jundayw\Tokenable\Exceptions;

use Throwable;

class AccessTokenExpiredException extends TokenExpiredException
{
    public function __construct(string $message = 'AccessTokenExpired', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
