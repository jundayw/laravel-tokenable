<?php

namespace Jundayw\Tokenable\Exceptions;

use Throwable;

class RefreshTokenNotAvailableException extends TokenInvalidException
{
    public function __construct(string $message = 'RefreshTokenNotAvailable', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
