<?php

namespace Jundayw\Tokenable\Exceptions;

use Throwable;

class RefreshTokenExpiredException extends TokenExpiredException
{
    public function __construct(string $message = 'RefreshTokenExpired', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
