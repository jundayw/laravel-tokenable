<?php

namespace Jundayw\Tokenable\Grants;

use Jundayw\Tokenable\Contracts\Auth\TokenableAuthGuard;

abstract class Grant
{
    protected TokenableAuthGuard $guard;

    /**
     * Get the current grant instance.
     *
     * @return TokenableAuthGuard|null
     */
    public function getGuard(): ?TokenableAuthGuard
    {
        return $this->guard;
    }

    /**
     * Set the grant instance to be used.
     *
     * @param TokenableAuthGuard $guard
     *
     * @return static
     */
    public function usingGuard(TokenableAuthGuard $guard): static
    {
        $this->guard = $guard;

        return $this;
    }
}
