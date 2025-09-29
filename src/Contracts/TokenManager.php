<?php

namespace Jundayw\Tokenable\Contracts;

use Closure;

interface TokenManager
{
    /**
     * Get a token driver instance.
     *
     * @param string|null $name
     *
     * @return Token
     */
    public function driver(?string $name = null): Token;

    /**
     * Create a hash token based token driver.
     *
     * @param string $name
     * @param array  $config
     *
     * @return Token
     */
    public function createHashTokenDriver(string $name, array $config): Token;

    /**
     * Create a jwt token based token driver.
     *
     * @param string $name
     * @param array  $config
     *
     * @return Token
     */
    public function createJwtTokenDriver(string $name, array $config): Token;

    /**
     * Register a custom driver creator Closure.
     *
     * @param string  $driver
     * @param Closure $callback
     *
     * @return static
     */
    public function extend(string $driver, Closure $callback): static;

    /**
     * Normalize the given driver name.
     *
     * Ensures the driver is supported (built-in or custom) and
     * returns the normalized driver name. Returns null if the
     * driver is not supported.
     *
     * @param string|null $driver
     *
     * @return string|null
     */
    public function normalizeDriverName(string $driver = null): ?string;
}
