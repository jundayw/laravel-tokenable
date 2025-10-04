<?php

namespace Jundayw\Tokenable\Concerns\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant;

trait TokenableAuthHelpers
{
    /**
     * Log the given user ID into the application without maintaining session state.
     *
     * @param mixed $id
     *
     * @return AuthorizationCodeGrant|null
     */
    public function onceUsingId(mixed $id): AuthorizationCodeGrant|null
    {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            return $this->setUser($user)->getAuthorizationCodeGrant()->setTokenable($user);
        }

        return null;
    }

    /**
     * Log a user into the application without maintaining session state.
     *
     * @param array $credentials
     *
     * @return AuthorizationCodeGrant|null
     */
    public function once(array $credentials = []): AuthorizationCodeGrant|null
    {
        if (!is_null($user = $this->provider->retrieveByCredentials($credentials))) {
            return $this->setUser($user)->getAuthorizationCodeGrant()->setTokenable($user);
        }

        return null;
    }

    /**
     * Log the given auth code into the application.
     *
     * @return AuthorizationCodeGrant|null
     */
    public function fromAuthCode(): AuthorizationCodeGrant|null
    {
        if (!is_null($user = $this->getAuthorizationCodeGrant()->fromAuthCode($this->request)?->getTokenable())) {
            return $this->setUser($user)->getAuthorizationCodeGrant();

        }

        return null;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     *
     * @return AccessTokenGrant|null
     */
    public function attempt(array $credentials = []): AccessTokenGrant|null
    {
        if (!is_null($user = $this->provider->retrieveByCredentials($credentials))) {
            return $this->login($user);
        }

        return null;
    }

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     *
     * @return AccessTokenGrant|null
     */
    public function loginUsingId(mixed $id): AccessTokenGrant|null
    {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            return $this->login($user);
        }

        return null;
    }

    /**
     * Log a user into the application.
     *
     * @param Authenticatable $user
     *
     * @return AccessTokenGrant
     */
    public function login(Authenticatable $user): AccessTokenGrant
    {
        $this->setUser($user)->fireLoginEvent($user);

        return $this->getAccessTokenGrant()->setTokenable($user);
    }

    /**
     * Log the user out of the application.
     *
     * @return bool
     */
    public function logout(): bool
    {
        if (is_null($user = $this->user())) {
            return false;
        }

        return tap($this->revokeToken(), fn() => $this->forgetUser()->fireLogoutEvent($user));
    }
}
