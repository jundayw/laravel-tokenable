<?php

namespace Jundayw\Tokenable;

use Closure;
use Illuminate\Container\Container;
use InvalidArgumentException;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Blacklist;
use Jundayw\Tokenable\Contracts\Grant\Grant;
use Jundayw\Tokenable\Contracts\Whitelist;
use Jundayw\Tokenable\Grants\TokenableGrant;
use Jundayw\Tokenable\Grants\TransientGrant;

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

    public function __construct(protected Container $app)
    {
        $this->extend(Contracts\Grant\TokenableGrant::class, fn() => $this->createTokenableGrantDriver());
        $this->extend(Contracts\Grant\TransientGrant::class, fn() => $this->createTransientGrantDriver());
    }

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
     * Create a tokenable grant based grant driver.
     *
     * @return Contracts\Grant\TokenableGrant
     */
    public function createTokenableGrantDriver(): Contracts\Grant\TokenableGrant
    {
        return new TokenableGrant(
            $this->app[Authenticable::class],
            $this->app[Contracts\Token\Factory::class],
            $this->app[Blacklist::class],
            $this->app[Whitelist::class],
            $this->app['cache.store'],
        );
    }

    /**
     * Create a transient grant based grant driver.
     *
     * @return Contracts\Grant\TransientGrant
     */
    public function createTransientGrantDriver(): Contracts\Grant\TransientGrant
    {
        return new TransientGrant(
            $this->app[Authenticable::class],
            $this->app[Contracts\Token\Factory::class],
            $this->app['cache.store'],
        );
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return Contracts\Grant\TokenableGrant::class;
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
