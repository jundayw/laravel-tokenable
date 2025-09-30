<?php

namespace Jundayw\Tokenable;

use Closure;
use InvalidArgumentException;
use Jundayw\Tokenable\Contracts\Grant\Grant;
use Jundayw\Tokenable\Contracts\Grant\TokenableGrant;

class GrantManager implements Contracts\Grant\Factory
{
    /**
     * The registered custom driver creators.
     *
     * @var string[]
     */
    protected array $customCreators = [];

    /**
     * The array of resolved grant drivers.
     *
     * @var Grant[]
     */
    protected array $grants = [];

    /**
     * Get a grant instance.
     *
     * @param string|null $name
     *
     * @return Grant
     */
    public function driver(string $name = null): Grant
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->drivers[$name] ??= $this->resolve($name);
    }

    /**
     * Resolve the given grant driver instance.
     *
     * If no driver name is provided, the default driver will be used.
     *
     * @param string $driver
     *
     * @return Grant
     */
    protected function resolve(string $driver): Grant
    {
        if (array_key_exists($driver, $this->customCreators)) {
            return call_user_func($this->customCreators[$driver]);
        }

        throw new InvalidArgumentException("Driver [{$driver}] is not defined.");
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return TokenableGrant::class;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string  $driver
     * @param Closure $callback
     *
     * @return static
     */
    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return call_user_func_array([$this->driver(), $method], $parameters);
    }
}
