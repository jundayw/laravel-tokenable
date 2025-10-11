<?php

namespace Jundayw\Tokenable\Contracts\Grant;

use Illuminate\Http\Request;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable;

interface AccessTokenGrant extends Grant
{
    /**
     * Attempt to resolve the authenticated tokenable model from the given request.
     *
     * This method extracts the access token from the request, validates it through
     * the token manager and authentication provider, and ensures the associated
     * tokenable model supports tokens. If validation passes, the tokenable model
     * is returned with its current access token attached; otherwise, null is returned.
     *
     * @param Request $request
     *     The incoming HTTP request that may contain an access token.
     *
     * @return Tokenable|null
     *     The resolved tokenable model if authentication succeeds, or null on failure.
     */
    public function findToken(Request $request): ?Tokenable;

    /**
     * Create a new access token for the user.
     *
     * @param string $name
     * @param string $platform
     * @param array  $scopes
     *
     * @return Token|null
     */
    public function createToken(string $name, string $platform = 'default', array $scopes = []): ?Token;

    /**
     * Refresh the access and refresh tokens using the current refresh token.
     *
     * @param Request $request
     *
     * @return Token|null
     */
    public function refreshToken(Request $request): ?Token;

    /**
     * Revoke (invalidate) the both access and refresh tokens by access token.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function revokeToken(Request $request): bool;

    /**
     * Returns a cloned instance of the current object with a modified suspension state.
     *
     * This allows temporarily ignoring the suspended status for method chaining
     * without affecting the original object.
     *
     * @return static A cloned instance with suspension state adjusted
     */
    public function withoutSuspension(): static;
}
