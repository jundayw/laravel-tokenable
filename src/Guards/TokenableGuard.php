<?php

namespace Jundayw\Tokenable\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use Jundayw\Tokenable\Concerns\Auth\TokenableHelpers;
use Jundayw\Tokenable\Contracts\Auth\SupportsTokenable;

class TokenableGuard implements Guard, SupportsTokenable
{
    use GuardHelpers, TokenableHelpers, Macroable;

    public function __construct(
        protected string $name,
        protected Repository $config,
        protected Auth $auth,
        protected Request $request,
        UserProvider $provider
    ) {
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        if (!empty($token)) {
            $user = $this->provider->retrieveByCredentials([]);
        }

        return $this->user = $user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        return !is_null($this->provider->retrieveByCredentials($credentials));
    }
}
