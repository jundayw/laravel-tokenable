<?php

namespace Jundayw\Tokenable\Contracts\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Jundayw\Tokenable\Contracts\Grant\Grant;
use Jundayw\Tokenable\Contracts\Grant\TransientGrant;
use Jundayw\Tokenable\Contracts\Tokenable;

interface TokenableAuthGuard
{
    /**
     * Log the given user ID into the application without maintaining session state.
     *
     * @param mixed $id
     *
     * @return TransientGrant|null
     */
    public function onceUsingId(mixed $id): TransientGrant|null;

    /**
     * Log a user into the application without maintaining session state.
     *
     * @param array $credentials
     *
     * @return TransientGrant|null
     */
    public function once(array $credentials = []): TransientGrant|null;

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     *
     * @return Grant|null
     */
    public function attempt(array $credentials = []): Grant|null;

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     *
     * @return Grant|null
     */
    public function loginUsingId(mixed $id): Grant|null;

    /**
     * Log a user into the application.
     *
     * @param Authenticatable $user
     *
     * @return Grant|null
     */
    public function login(Authenticatable $user): Grant|null;

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
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getConfig(string $key, mixed $default = null): mixed;

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
}
