<?php

namespace Jundayw\Tokenable\Repositories;

use Illuminate\Cache\Repository;
use Jundayw\Tokenable\Contracts\Blacklist;

class BlacklistRepository extends Repository implements Blacklist
{
    /**
     * The blacklist flag.
     *
     * @var bool
     */
    protected bool $blacklistEnabled = false;

    /**
     * Check if blacklist functionality is enabled.
     *
     * @return bool True if blacklist is enabled, false otherwise.
     */
    public function isBlacklistEnabled(): bool
    {
        return $this->blacklistEnabled;
    }

    /**
     * Enable or disable blacklist functionality.
     *
     * @param bool $blacklistEnabled
     *
     * @return static Returns the current instance for method chaining.
     */
    public function setBlacklistEnabled(bool $blacklistEnabled): static
    {
        $this->blacklistEnabled = $blacklistEnabled;

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
