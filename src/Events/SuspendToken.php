<?php

namespace Jundayw\Tokenable\Events;

use Illuminate\Queue\SerializesModels;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Tokenable;

class SuspendToken
{
    use SerializesModels;

    public function __construct(
        protected bool $global = false,
        protected Authenticable $authorization,
        protected Tokenable $tokenable,
    ) {
        //
    }

    /**
     * Determine whether the suspension is applied at the account level.
     *
     * @return bool
     */
    public function isGlobal(): bool
    {
        return $this->global;
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
}
