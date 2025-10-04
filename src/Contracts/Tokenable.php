<?php

namespace Jundayw\Tokenable\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;

interface Tokenable
{
    /**
     * Set the current access token for the user.
     *
     * @param Authenticable $authentication
     *
     * @return static
     */
    public function withAccessToken(Authenticable $authentication): static;

    /**
     * Get the current access token being used by the user.
     *
     * @return Authenticable|null
     */
    public function token(): ?Authenticable;

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function tokenCan(string $scope): bool;

    /**
     * Suspends token usage.
     *
     * When invoked, this method prevents access using tokens, either at the account level
     * or for the current platform-specific token, depending on the `$global` flag.
     *
     * After calling this method, subsequent calls to `createToken` may return
     * an authorization code (`auth code`) instead of a directly usable access/refresh token.
     * To regain access, the client must exchange the returned `auth code` for new valid tokens.
     *
     * @param bool $global Determines the suspension scope:
     *                     - true: suspend at the account level, affecting all issued tokens.
     *                     - false: suspend only the current token for its platform type.
     *
     * @return bool Returns true if the suspension was successfully applied, false otherwise.
     */
    public function suspendToken(bool $global = false): bool;

    /**
     * Get the access tokens that belong to model.
     *
     * @return MorphMany
     */
    public function tokens(): MorphMany;

    /**
     * Get the abilities that the user did have.
     *
     * @return array
     */
    public function getScopes(): array;

    /**
     * Return the identifier for the `sub` claim.
     *
     * @return string|int
     */
    public function getJWTIdentifier(): int|string;

    /**
     * Return the issuer for the `iss` claim.
     *
     * @return string
     */
    public function getJWTIssuer(): string;

    /**
     * Return the unique token ID for the `jti` claim.
     *
     * @return string
     */
    public function getJWTId(): string;

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array;
}
