<?php

namespace Jundayw\Tokenable\Tokens;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Cookie as CookieJar;
use Illuminate\Support\Str;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Token\Token as TokenContract;
use Jundayw\Tokenable\Contracts\Tokenable as TokenableContract;
use Jundayw\Tokenable\Tokenable;
use Symfony\Component\HttpFoundation\Cookie;

abstract class Token implements TokenContract
{
    public string        $name;
    protected Repository $config;

    protected ?string $accessToken       = null;
    protected ?string $refreshToken      = null;
    protected ?string $authorizationCode = null;

    protected string $expiresIn;
    protected array  $attributes = [];

    /**
     * Get the name of the instance.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the instance.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the configuration repository instance.
     *
     * @return Repository
     */
    public function getConfig(): Repository
    {
        return $this->config;
    }

    /**
     * Set the configuration repository instance.
     *
     * @param Repository $config
     *
     * @return static
     */
    public function setConfig(Repository $config): static
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get the current access token value.
     *
     * @return string|null
     */
    final public function getAccessToken(): ?string
    {
        return hash('sha256', $this->accessToken);
    }

    /**
     * Set the raw access token value before hashing.
     *
     * @param string $token
     *
     * @return static
     */
    public function setAccessToken(string $token): static
    {
        $this->accessToken = $token;
        return $this;
    }

    /**
     * Get the current refresh token value.
     *
     * @return string|null
     */
    final public function getRefreshToken(): ?string
    {
        return hash('sha512', $this->refreshToken);
    }

    /**
     * Set the raw refresh token value before hashing.
     *
     * @param string $token
     *
     * @return static
     */
    public function setRefreshToken(string $token): static
    {
        $this->refreshToken = $token;
        return $this;
    }

    /**
     * Get the current auth code value.
     *
     * @return string|null
     */
    final public function getAuthorizationCode(): ?string
    {
        return hash('sha384', $this->authorizationCode);
    }

    /**
     * Set the raw auth code value before hashing.
     *
     * @param string $code
     *
     * @return static
     */
    public function setAuthorizationCode(string $code): static
    {
        $this->authorizationCode = $code;
        return $this;
    }

    /**
     * Get the number of seconds until the access token expires.
     *
     * @return string
     */
    public function getExpiresIn(): string
    {
        return $this->expiresIn ?? now()->toIso8601ZuluString();
    }

    /**
     * Checks if the given key exists in the attributes array.
     *
     * @param string $key The attribute key to check
     *
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Retrieves the value associated with the given key from the attributes array.
     *
     * If the key does not exist, the provided default value is returned.
     *
     * @param string $key     The attribute key to retrieve
     * @param mixed  $default The default value to return if the key does not exist (default: null)
     *
     * @return mixed The value of the attribute, or the default value if not present
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Generate a new access token.
     *
     * This token is typically short-lived and is used to authenticate API requests.
     *
     * @param Authenticable     $authentication
     * @param TokenableContract $tokenable
     *
     * @return string
     */
    abstract protected function generateAccessToken(Authenticable $authentication, TokenableContract $tokenable): string;

    /**
     * Generate a new refresh token.
     *
     * Refresh tokens are long-lived and used to obtain new access tokens.
     *
     * @param Authenticable     $authentication
     * @param TokenableContract $tokenable
     *
     * @return string
     */
    abstract protected function generateRefreshToken(Authenticable $authentication, TokenableContract $tokenable): string;

    /**
     * Generate a new auth code.
     *
     * The authentication code has a short expiration period; it is used to obtain a new token pair.
     *
     * @param Authenticable     $authentication
     * @param TokenableContract $tokenable
     *
     * @return string
     */
    abstract protected function generateAuthorizationCode(Authenticable $authentication, TokenableContract $tokenable): string;

    /**
     * Generate a unique access token for the given authorizable and tokenizable.
     *
     * @param Authenticable     $authentication
     * @param TokenableContract $tokenable
     *
     * @return string
     */
    protected function generateUniqueAccessToken(Authenticable $authentication, TokenableContract $tokenable): string
    {
        return $authentication->newQuery()
            ->where('access_token', $token = $this->generateAccessToken($authentication, $tokenable))
            ->exists() ? $this->generateUniqueAccessToken($authentication, $tokenable) : $token;
    }

    /**
     * Generate a unique refresh token for the given authorizable and tokenizable.
     *
     * @param Authenticable     $authentication
     * @param TokenableContract $tokenable
     *
     * @return string
     */
    protected function generateUniqueRefreshToken(Authenticable $authentication, TokenableContract $tokenable): string
    {
        return $authentication->newQuery()
            ->where('refresh_token', $token = $this->generateRefreshToken($authentication, $tokenable))
            ->exists() ? $this->generateUniqueRefreshToken($authentication, $tokenable) : $token;
    }

    /**
     * Build an access token and refresh token pair from given values.
     *
     * @param Authenticable     $authentication
     * @param TokenableContract $tokenable
     *
     * @return static
     */
    public function buildTokens(Authenticable $authentication, TokenableContract $tokenable): static
    {
        $this->accessToken  = $this->generateUniqueAccessToken($authentication, $tokenable);
        $this->refreshToken = $this->generateUniqueRefreshToken($authentication, $tokenable);
        $this->expiresIn    = $authentication->getAttribute('access_token_expire_at')->toIso8601ZuluString();
        $this->attributes   = [
            'access_token'  => $this->accessToken,
            'token_type'    => $this->getResponseType(),
            'expires_in'    => $this->getExpiresIn(),
            'refresh_token' => $this->refreshToken,
            'type'          => 'token',
        ];

        return $this;
    }

    /**
     * Build an auth code from given values.
     *
     * @param Authenticable     $authentication
     * @param TokenableContract $tokenable
     *
     * @return static
     */
    public function buildAuthCode(Authenticable $authentication, TokenableContract $tokenable): static
    {
        $this->authorizationCode = $this->generateAuthorizationCode($authentication, $tokenable);
        $this->attributes        = [
            'auth_code' => $this->authorizationCode,
            'code_type' => $this->getResponseType(),
            'type'      => 'code',
        ];

        return $this;
    }

    /**
     * Returns the identifier as a RFC 9562/4122 case-insensitive string.
     *
     * @see     https://datatracker.ietf.org/doc/html/rfc9562/#section-4
     *
     * @example 09748193-048a-4bfb-b825-8528cf74fdc1 (len=36)
     * @return string
     */
    public function ulid(): string
    {
        return Str::ulid()->toRfc4122();
    }

    /**
     * Get the list of cookies associated with the current instance.
     *
     * @return Cookie[]
     */
    public function getCookies(): array
    {
        $cookies = [];

        foreach ($this->toArray() as $name => $value) {
            $cookies[$name] = CookieJar::forever($name, $value);
        }

        return $cookies;
    }

    /**
     * Queue cookies to send with the next response.
     *
     * @return static
     */
    public function withCookies(): static
    {
        foreach ($this->getCookies() as $cookie) {
            CookieJar::queue($cookie);
        }

        return $this;
    }

    /**
     * Expire cookies.
     *
     * @return static
     */
    public function withoutCookies(): static
    {
        foreach ($this->getCookies() as $name => $cookie) {
            CookieJar::expire($name);
        }

        return $this;
    }

    /**
     * Determine the token type for the current driver.
     *
     * If the driver name matches the default driver defined in the
     * tokenable configuration, the token type will be returned as
     * "Bearer". Otherwise, the driver name itself will be used.
     *
     * @return string
     */
    protected function getResponseType(): string
    {
        return with(
            value: $this->getName() === config('tokenable.default.driver'),
            callback: fn(bool $default) => $default ? 'Bearer' : $this->getName()
        );
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    #[\Override]
    public function toArray(): array
    {
        if (is_callable($tokenable = Tokenable::tokenable())) {
            return Closure::bind($tokenable, $this, static::class)($this->attributes, $this);
        }

        return $this->attributes;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    #[\Override]
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
