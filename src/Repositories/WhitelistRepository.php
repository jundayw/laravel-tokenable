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

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     */
    #[\Override]
    public function get($key, mixed $default = null): mixed
    {
        try {
            return parent::get($key, $default);
        } catch (\Throwable $e) {
            //
        }

        return $default;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     */
    #[\Override]
    public function has($key): bool
    {
        try {
            return parent::has($key);
        } catch (\Throwable $e) {
            //
        }

        return false;
    }
}
