<?php

namespace Jundayw\Tokenable\Events;

use Illuminate\Queue\SerializesModels;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable;

class AccessTokenEvent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Authenticable $authorization
     * @param Tokenable     $tokenable
     * @param Token         $token
     */
    public function __construct(
        protected Authenticable $authorization,
        protected Tokenable $tokenable,
        protected Token $token,
    ) {
        //
    }

    /**
     * Get the authorization entity instance.
     *
     * @return Authenticable
     */
    public function getAuthorization(): Authenticable
    {
        return $this->authorization;
    }

    /**
     * Get the tokenable entity instance.
     *
     * @return Tokenable
     */
    public function getTokenable(): Tokenable
    {
        return $this->tokenable;
    }

    /**
     * Get the token entity instance.
     *
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }
}
