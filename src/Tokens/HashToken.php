<?php

namespace Jundayw\Tokenable\Tokens;

use Illuminate\Config\Repository;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Tokenable;

class HashToken extends Token
{
    public function __construct(string $name, array $config)
    {
        $this->name   = $name;
        $this->config = new Repository($config);
    }

    /**
     * Generate a new access token.
     *
     * This token is typically short-lived and is used to authenticate API requests.
     *
     * @param Authenticable $authentication
     * @param Tokenable     $tokenable
     *
     * @return string
     */
    protected function generateAccessToken(Authenticable $authentication, Tokenable $tokenable): string
    {
        return hash_hmac($this->getConfig()->get('algo'), $this->ulid(), $this->getConfig()->get('secret'));
    }

    /**
     * Generate a new refresh token.
     *
     * Refresh tokens are long-lived and used to obtain new access tokens.
     *
     * @param Authenticable $authentication
     * @param Tokenable     $tokenable
     *
     * @return string
     */
    protected function generateRefreshToken(Authenticable $authentication, Tokenable $tokenable): string
    {
        return hash_hmac($this->getConfig()->get('algo'), $this->ulid(), $this->getConfig()->get('secret'));
    }

    /**
     * Generate a new auth code.
     *
     * The authentication code has a short expiration period; it is used to obtain a new token pair.
     *
     * @param Authenticable $authentication
     * @param Tokenable     $tokenable
     *
     * @return string
     */
    protected function generateAuthorizationCode(Authenticable $authentication, Tokenable $tokenable): string
    {
        return hash_hmac($this->getConfig()->get('algo'), $this->ulid(), $this->getConfig()->get('secret'));
    }

    /**
     * Validate the token using a validator.
     *
     * @param string $token
     *
     * @return bool
     */
    public function validate(string $token): bool
    {
        return true;
    }
}
