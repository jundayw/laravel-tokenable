<?php

namespace Jundayw\Tokenable\Contracts\Grant;

use Illuminate\Http\Request;
use Jundayw\Tokenable\Contracts\Token\Token;

interface AuthorizationCodeGrant extends Grant
{
    /**
     * Log the given auth code into the application.
     *
     * @param Request $request
     *
     * @return static|null
     */
    public function fromAuthCode(Request $request): ?static;

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
     * Create a new authorization code token for the current tokenable entity.
     *
     * This method attempts to generate an authorization code for the
     * authenticated tokenable. If no tokenable is available, it will
     * return null. When successfully created, the authorization code
     * token is stored in the repository with a configured time-to-live.
     *
     * @return Token|null  The generated authorization code token, or null if unavailable.
     */
    public function createAuthCode(): ?Token;
}
