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
use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant;
use Jundayw\Tokenable\Contracts\Grant\Factory as GrantFactoryContract;
use Jundayw\Tokenable\Contracts\Grant\RefreshTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\RevokeTokenGrant;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable;

class TokenableGuard implements Guard, TokenableAuthGuard
{
    use GuardHelpers, TokenableAuthHelpers, Macroable;

    public function __construct(
        protected string $name,
        protected Repository $config,
        protected GrantFactoryContract $grant,
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

        if (!is_null($this->user = $this->getAccessTokenGrant()->findToken($this->request))) {
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
     * Revoke (invalidate) the both access and refresh tokens by access token.
     *
     * @return bool
     */
    public function revokeToken(): bool
    {
        if ($this->guest()) {
            return false;
        }

        return $this->forgetUser()->getRevokeTokenGrant()->revoke($this->request);
    }

    /**
     * Refresh the access and refresh tokens using the current refresh token.
     *
     * @return Token|null
     */
    public function refreshToken(): ?Token
    {
        return $this->getRefreshTokenGrant()->refresh($this->request);
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
     * Get the AccessTokenGrant instance.
     *
     * @return AccessTokenGrant
     */
    protected function getAccessTokenGrant(): AccessTokenGrant
    {
        return $this->grant->getAccessTokenGrant();
    }

    /**
     * Get the AuthorizationCodeGrant instance.
     *
     * @return AuthorizationCodeGrant
     */
    protected function getAuthorizationCodeGrant(): AuthorizationCodeGrant
    {
        return $this->grant->getAuthorizationCodeGrant();
    }

    /**
     * Get the RefreshTokenGrant instance.
     *
     * @return RefreshTokenGrant
     */
    protected function getRefreshTokenGrant(): RefreshTokenGrant
    {
        return $this->grant->getRefreshTokenGrant();
    }

    /**
     * Get the RevokeTokenGrant instance.
     *
     * @return RevokeTokenGrant
     */
    protected function getRevokeTokenGrant(): RevokeTokenGrant
    {
        return $this->grant->getRevokeTokenGrant();
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
