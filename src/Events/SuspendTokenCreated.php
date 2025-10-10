<?php

namespace Jundayw\Tokenable\Events;

use Jundayw\Tokenable\Contracts\Auth\Authenticable;

class SuspendTokenCreated extends AccessTokenEvent
{
    public function __construct(
        protected bool $global = false,
        protected Authenticable $authorization,
    ) {
        parent::__construct($authorization);
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
}
