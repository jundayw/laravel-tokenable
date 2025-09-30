<?php

namespace Jundayw\Tokenable\Guards;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use Jundayw\Tokenable\Concerns\Auth\TokenableAuthHelpers;
use Jundayw\Tokenable\Contracts\Auth\TokenableAuthGuard;
use Jundayw\Tokenable\Contracts\Grant\Factory as GrantFactoryContract;
use Jundayw\Tokenable\Contracts\Grant\TokenableGrant;
use Jundayw\Tokenable\Contracts\Grant\TransientGrant;
use Jundayw\Tokenable\Contracts\Tokenable;

class TokenableGuard implements Guard, TokenableAuthGuard
{
    use GuardHelpers, TokenableAuthHelpers, Macroable;

    public function __construct(
        protected string $name,
        protected Repository $config,
        protected GrantFactoryContract $grantManager,
        protected Request $request,
        ?UserProvider $provider
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

        if (!is_null($this->user = $this->getTokenableGrant()->findAccessToken($this->request))) {
            $this->fireAuthenticatedEvent($this->user);
        }

        return $this->user;
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
        return !is_null($this->provider?->retrieveByCredentials($credentials));
    }

    /**
     * Fire the authenticated event.
     *
     * @param Authenticatable $user
     *
     * @return void
     */
    protected function fireAuthenticatedEvent(Authenticatable $user): void
    {
        event(new Authenticated($this->name, $user));
    }

    /**
     * Fire the login event.
     *
     * @param Authenticatable $user
     *
     * @return void
     */
    protected function fireLoginEvent(Authenticatable $user): void
    {
        event(new Login($this->name, $user, false));
    }

    /**
     * Fire the logout event.
     *
     * @param Authenticatable $user
     *
     * @return void
     */
    protected function fireLogoutEvent(Authenticatable $user): void
    {
        event(new Logout($this->name, $user));
    }

    /**
     * Get the name associated with the instance.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the specified configuration value.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->config->get($key, $default);
    }

    /**
     * Return the currently cached user.
     *
     * @return Authenticatable|Tokenable|Model|null
     */
    public function getUser(): Authenticatable|Tokenable|Model|null
    {
        if ($this->hasUser()) {
            return $this->user;
        }

        return null;
    }

    /**
     * Get the tokenable grant driver instance.
     *
     * This grant type manages access tokens and refresh tokens
     * for tokenable models (e.g., users, clients, devices).
     *
     * @return TokenableGrant
     */
    public function getTokenableGrant(): TokenableGrant
    {
        return $this
            ->grantManager
            ->driver(TokenableGrant::class)
            ->usingGuard($this);
    }

    /**
     * Get the transient grant driver instance.
     *
     * This grant type issues short-lived authorization codes
     * that can be exchanged for access tokens.
     *
     * @return TransientGrant
     */
    public function getTransientGrant(): TransientGrant
    {
        return $this
            ->grantManager
            ->driver(TransientGrant::class)
            ->usingGuard($this);
    }

    /**
     * Set the current request instance.
     *
     * @param Request $request
     *
     * @return static
     */
    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }
}
