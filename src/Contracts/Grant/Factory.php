<?php

namespace Jundayw\Tokenable\Contracts\Grant;

use Closure;

interface Factory
{
    /**
     * Get a grant instance.
     *
     * @param string|null $name
     *
     * @return Grant
     */
    public function driver(string $name = null): Grant;

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string;

    /**
     * Register a custom driver creator Closure.
     *
     * @param string  $driver
     * @param Closure $callback
     *
     * @return static
     */
    public function extend(string $driver, Closure $callback): static;
}
