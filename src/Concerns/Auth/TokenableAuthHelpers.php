<?php

namespace Jundayw\Tokenable\Concerns\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Jundayw\Tokenable\Contracts\Grant\TokenableGrant;
use Jundayw\Tokenable\Contracts\Grant\TransientGrant;

trait TokenableAuthHelpers
{
    /**
     * Log the given user ID into the application without maintaining session state.
     *
     * @param mixed $id
     *
     * @return TransientGrant|null
     */
    public function onceUsingId(mixed $id): TransientGrant|null
    {
        if (!is_null($user = $this->provider->retrieveById($id))) {
            return $this->setUser($user)->getTransientGrant();
        }

        return null;
    }

    /**
     * Log a user into the application without maintaining session state.
     *
     * @param array $credentials
     *
     * @return TransientGrant|null
     */
    public function once(array $credentials = []): TransientGrant|null
    {
        if (!is_null($user = $this->provider->retrieveByCredentials($credentials))) {
            return $this->setUser($user)->getTransientGrant();
        }

        return null;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     *
     * @return TokenableGrant|null
     */
    public function attempt(array $credentials = []): TokenableGrant|null
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
     * @return TokenableGrant|null
     */
    public function loginUsingId(mixed $id): TokenableGrant|null
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
     * @return TokenableGrant|null
     */
    public function login(Authenticatable $user): TokenableGrant|null
    {
        $this->setUser($user)->fireLoginEvent($user);

        return $this->getTokenableGrant();
    }

    /**
     * Log the user out of the application.
     *
     * @return bool
     */
    public function logout(): bool
    {
        if ($this->guest()) {
            return false;
        }

        $user = $this->user;

        $this->forgetUser()->fireLogoutEvent($user);

        return true;
    }
}
