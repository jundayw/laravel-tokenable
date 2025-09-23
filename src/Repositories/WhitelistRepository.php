<?php

namespace Jundayw\Tokenable\Repositories;

use Illuminate\Cache\Repository;
use Jundayw\Tokenable\Contracts\Whitelist;

class WhitelistRepository extends Repository implements Whitelist
{
    /**
     * The whitelist flag.
     *
     * @var bool
     */
    protected bool $whitelistEnabled = false;

    /**
     * Check if whitelist functionality is enabled.
     *
     * @return bool True if whitelist is enabled, false otherwise.
     */
    public function isWhitelistEnabled(): bool
    {
        return $this->whitelistEnabled;
    }

    /**
     * Enable or disable whitelist functionality.
     *
     * @param bool $whitelistEnabled
     *
     * @return static Returns the current instance for method chaining.
     */
    public function setWhitelistEnabled(bool $whitelistEnabled): static
    {
        $this->whitelistEnabled = $whitelistEnabled;

        return $this;
    }
}
