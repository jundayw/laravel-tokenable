<?php

namespace Jundayw\Tokenable\Contracts\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable;

interface TokenableAuthGuard extends Guard
{
    /**
     * Log the given user ID into the application without maintaining session state.
     *
     * @param mixed $id
     *
     * @return AuthorizationCodeGrant|null
     */
    public function onceUsingId(mixed $id): AuthorizationCodeGrant|null;

    /**
     * Log a user into the application without maintaining session state.
     *
     * @param array $credentials
     *
     * @return AuthorizationCodeGrant|null
     */
    public function once(array $credentials = []): AuthorizationCodeGrant|null;

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     *
     * @return AccessTokenGrant|null
     */
    public function attempt(array $credentials = []): AccessTokenGrant|null;

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     *
     * @return AccessTokenGrant|null
     */
    public function loginUsingId(mixed $id): AccessTokenGrant|null;

    /**
     * Log a user into the application.
     *
     * @param Authenticatable $user
     *
     * @return AccessTokenGrant|null
     */
    public function login(Authenticatable $user): AccessTokenGrant|null;

    /**
     * Log the user out of the application.
     *
     * @return bool
     */
    public function logout(): bool;

    /**
     * Get the name associated with the instance.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the specified configuration value.
     *
     * @return Repository
     */
    public function getConfig(): Repository;

    /**
     * Return the currently cached user.
     *
     * @return Authenticatable|Tokenable|Model|null
     */
    public function getUser(): Authenticatable|Tokenable|Model|null;

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|Tokenable|Model|null
     */
    public function user(): Authenticatable|Tokenable|Model|null;

    /**
     * Revoke (invalidate) the both access and refresh tokens by access token.
     *
     * @return bool
     */
    public function revokeToken(): bool;

    /**
     * Refresh the access and refresh tokens using the current refresh token.
     *
     * @return Token|null
     */
    public function refreshToken(): ?Token;
}
