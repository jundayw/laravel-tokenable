<?php

namespace Jundayw\Tokenable;

use Closure;
use InvalidArgumentException;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Tokens\HashToken;
use Jundayw\Tokenable\Tokens\JsonWebToken;

class TokenManager implements Contracts\Token\Factory
{
    /**
     * The registered custom driver creators.
     *
     * @var string[]
     */
    protected array $customCreators = [];

    /**
     * The array of resolved token drivers.
     *
     * @var Token[]
     */
    protected array $drivers = [];

    /**
     * Get a token driver instance.
     *
     * @param string|null $name
     *
     * @return Token
     */
    public function driver(string $name = null): Token
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->drivers[$name] ??= $this->resolve($name);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return config('tokenable.default.driver', 'hash');
    }

    /**
     * Get the driver configuration.
     *
     * @param string $name
     *
     * @return array|null
     */
    protected function getConfig(string $name): ?array
    {
        return config("tokenable.drivers.{$name}");
    }

    /**
     * Resolve the given token driver instance.
     *
     * If no driver name is provided, the default driver will be used.
     *
     * @param string $driver
     *
     * @return Token
     */
    protected function resolve(string $driver): Token
    {
        $config = $this->getConfig($driver);

        if (is_null($config)) {
            throw new InvalidArgumentException("Token driver [{$driver}] is not defined.");
        }

        if (array_key_exists($driver, $this->customCreators)) {
            return call_user_func($this->customCreators[$driver], $config);
        }

        $driverMethod = 'create' . ucfirst($driver) . 'TokenDriver';

        if (method_exists($this, $driverMethod)) {
            return call_user_func([$this, $driverMethod], $driver, $config);
        }

        throw new InvalidArgumentException("Token driver [{$driver}] is not defined.");
    }

    /**
     * Create a hash token based token driver.
     *
     * @param string $name
     * @param array  $config
     *
     * @return Token
     */
    public function createHashTokenDriver(string $name, array $config): Token
    {
        return new HashToken($name, $config);
    }

    /**
     * Create a jwt token based token driver.
     *
     * @param string $name
     * @param array  $config
     *
     * @return Token
     */
    public function createJwtTokenDriver(string $name, array $config): Token
    {
        return new JsonWebToken($name, $config);
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
    public function normalizeDriverName(string $driver = null): ?string
    {
        $drivers = array_merge(['hash', 'jwt'], array_keys($this->customCreators));

        return in_array($driver, $drivers) ? $driver : null;
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
