<?php

namespace Jundayw\Tokenable\Contracts\Grant;

use Jundayw\Tokenable\Contracts\Auth\TokenableAuthGuard;

interface Grant
{
    /**
     * Get the current grant instance.
     *
     * @return TokenableAuthGuard|null
     */
    public function getGuard(): ?TokenableAuthGuard;

    /**
     * Set the grant instance to be used.
     *
     * @param TokenableAuthGuard $guard
     *
     * @return static
     */
    public function usingGuard(TokenableAuthGuard $guard): static;
}
