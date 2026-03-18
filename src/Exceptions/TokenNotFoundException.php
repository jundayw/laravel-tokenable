<?php

namespace Jundayw\Tokenable\Exceptions;

use Throwable;

class TokenNotFoundException extends TokenInvalidException
{
    public function __construct(string $message = 'TokenNotFound', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
