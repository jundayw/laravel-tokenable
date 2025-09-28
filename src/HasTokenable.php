<?php

namespace Jundayw\Tokenable;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;

trait HasTokenable
{
    /**
     * The current access token for the authentication user.
     *
     * @var Authenticable|null
     */
    protected ?Authenticable $authentication = null;

    /**
     * Set the current access token for the user.
     *
     * @param Authenticable $authentication
     *
     * @return static
     */
    public function withAccessToken(Authenticable $authentication): static
    {
        $this->authentication = $authentication;

        return $this;
    }

    /**
     * Get the current access token being used by the user.
     *
     * @return Authenticable|null
     */
    public function token(): ?Authenticable
    {
        return $this->authentication;
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function tokenCan(string $scope): bool
    {
        return $this->token()?->can($scope) ?? false;
    }

    /**
     * Get the access tokens that belong to model.
     *
     * @return MorphMany
     */
    public function tokens(): MorphMany
    {
        return $this->morphMany(Tokenable::authenticationModel(), 'tokenable');
    }

    /**
     * Get the abilities that the user did have.
     *
     * @return array
     */
    public function getScopes(): array
    {
        return [];
    }

    /**
     * Return the identifier for the `sub` claim.
     *
     * @return string|int
     */
    public function getJWTIdentifier(): int|string
    {
        return $this->getKey();
    }

    /**
     * Return the issuer for the `iss` claim.
     *
     * @return string
     */
    public function getJWTIssuer(): string
    {
        return str_replace('\\', '.', get_class($this));
    }

    /**
     * Return the unique token ID for the `jti` claim.
     *
     * @return string
     */
    public function getJWTId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
