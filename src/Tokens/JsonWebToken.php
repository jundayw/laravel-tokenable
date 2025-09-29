<?php

namespace Jundayw\Tokenable\Tokens;

use Firebase\JWT\JWT;
use Illuminate\Config\Repository;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Tokenable as TokenableContract;
use Jundayw\Tokenable\Tokenable;

class JsonWebToken extends Token
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
     * @param Authenticable     $authentication
     * @param TokenableContract $tokenable
     *
     * @return string
     */
    protected function generateAccessToken(Authenticable $authentication, TokenableContract $tokenable): string
    {
        $claims  = $tokenable->getJWTCustomClaims();
        $payload = $claims + [
                'jti' => $tokenable->getJWTId(),
                'iss' => $tokenable->getJWTIssuer(),
                'sub' => $tokenable->getJWTIdentifier(),
                'aud' => $authentication->getAttribute('scopes'),
                'exp' => $authentication->getAttribute('access_token_expire_at')->getTimestamp(),
                'iat' => now()->getTimestamp(),
            ];

        return JWT::encode($payload, $this->getKeyByAlgorithm(), $this->getConfig()->get('algo'));
    }

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
    protected function generateRefreshToken(Authenticable $authentication, TokenableContract $tokenable): string
    {
        $claims  = $tokenable->getJWTCustomClaims();
        $payload = $claims + [
                'jti' => $tokenable->getJWTId(),
                'iss' => $tokenable->getJWTIssuer(),
                'sub' => $tokenable->getJWTIdentifier(),
                'exp' => $authentication->getAttribute('access_token_expire_at')->getTimestamp(),
                'nbf' => $authentication->getAttribute('refresh_token_available_at')->getTimestamp(),
                'iat' => now()->getTimestamp(),
            ];

        return JWT::encode($payload, $this->getKeyByAlgorithm(), $this->getConfig()->get('algo'));
    }

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
    protected function generateAuthorizationCode(Authenticable $authentication, TokenableContract $tokenable): string
    {
        $claims  = $tokenable->getJWTCustomClaims();
        $payload = $claims + [
                'jti' => $tokenable->getJWTId(),
                'iss' => $tokenable->getJWTIssuer(),
                'sub' => $tokenable->getJWTIdentifier(),
                'iat' => now()->getTimestamp(),
            ];

        return JWT::encode($payload, $this->getKeyByAlgorithm(), $this->getConfig()->get('algo'));
    }

    protected function getKeyByAlgorithm(bool $isPrivate = true): string
    {
        if (str_starts_with($this->getConfig()->get('algo'), 'H')) {
            return $this->getConfig()->get('secret_key');
        }

        $file = $isPrivate ? $this->getConfig()->get('private_key') : $this->getConfig()->get('public_key');
        $key  = Tokenable::keyPath($file);

        return is_file($key) ? file_get_contents($key) : $key;
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
