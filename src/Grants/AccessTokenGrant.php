<?php

namespace Jundayw\Tokenable\Grants;

use Illuminate\Http\Request;
use Jundayw\Tokenable\Concerns\Grant\AccessTokenHelper;
use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant as AccessTokenGrantContract;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable;
use Jundayw\Tokenable\Contracts\Tokenable as TokenableContract;
use Jundayw\Tokenable\Events\AccessTokenCreated;
use Jundayw\Tokenable\Events\AccessTokenRefreshed;
use Jundayw\Tokenable\Events\AccessTokenRevoked;
use Psr\SimpleCache\InvalidArgumentException;

class AccessTokenGrant extends Grant implements AccessTokenGrantContract
{
    use AccessTokenHelper;

    protected bool $suspended = false;

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
     * @return TokenableContract|null
     *     The resolved tokenable model if authentication succeeds, or null on failure.
     */
    public function findToken(Request $request): ?TokenableContract
    {
        if (is_null($token = $this->getAccessTokenFromRequest($request))) {
            return null;
        }

        $token = $this->getTokenManager()->driver(
            $this->getTokenManager()->normalizeDriverName($request->getUser())
        )->setAccessToken($token);

        try {
            if ($this->blacklist->isBlacklistEnabled() && $this->blacklist->has($token->getAccessToken())) {
                return null;
            }
        } catch (InvalidArgumentException $e) {
            return null;
        }

        try {
            if ($this->whitelist->isWhitelistEnabled() && $authentication = $this->whitelist->get($token->getAccessToken())) {
                $authentication->setRelation('tokenable', $authentication->getAttribute('tokenable'));
            } else {
                $authentication = $this->getAuthentication()->findAccessToken($token->getAccessToken());
            }
        } catch (InvalidArgumentException $e) {
            $authentication = $this->getAuthentication()->findAccessToken($token->getAccessToken());
        }

        if (is_null($authentication) ||
            !$this->isValidAuthenticationToken($authentication, $tokenable = $authentication->getRelation('tokenable')) ||
            !$this->supportsTokens($tokenable)) {
            return null;
        }

        $this->setAuthentication($authentication)->setTokenable($tokenable)->setToken($token);

        return tap($tokenable, static fn(TokenableContract $tokenable) => $tokenable->withAccessToken($authentication));
    }

    /**
     * Build an access token and refresh token pair from given values.
     *
     * @param string $name
     * @param string $platform
     * @param array  $scopes
     *
     * @return Token|null
     */
    public function createToken(string $name, string $platform = 'default', array $scopes = []): ?Token
    {
        if (is_null($tokenable = $this->getTokenable())) {
            return null;
        }

        // Create a new authorization code token for the current tokenable entity.
        if ($this->isSuspended($tokenable, $platform)) {
            return $this->getGuard()
                ->onceUsingId($this->getTokenable()->getKey())
                ->setToken($this->getToken())
                ->createAuthCode();
        }

        $accessExpireAt     = config('tokenable.ttl', 7200);
        $refreshAvailableAt = config('tokenable.refresh_nbf', 3600);
        $refreshExpireAt    = config('tokenable.refresh_ttl', 'P15D');
        $authentication     = $tokenable->tokens()->make([
            'name'                       => $name,
            'platform'                   => $platform,
            'scopes'                     => $this->getScopes($scopes),
            'access_token_expire_at'     => $this->getDateTimeAt($accessExpireAt),
            'refresh_token_available_at' => $this->getDateTimeAt($refreshAvailableAt),
            'refresh_token_expire_at'    => $this->getDateTimeAt($refreshExpireAt),
        ]);
        $token              = $this->getToken()->buildTokens($authentication, $tokenable);

        $this->setAuthentication($authentication)->setTokenable($tokenable)->setToken($token);

        return tap($token, function (Token $token) use ($authentication, $tokenable) {
            if ($authentication->fill([
                'token_driver'  => $token->getName(),
                'access_token'  => $token->getAccessToken(),
                'refresh_token' => $token->getRefreshToken(),
            ])->save()) {
                event(new AccessTokenCreated($this->getGuard()->getConfig(), $authentication));
            }
        });
    }

    /**
     * Returns a cloned instance of the current object with a modified suspension state.
     *
     * This allows temporarily ignoring the suspended status for method chaining
     * without affecting the original object.
     *
     * @return static A cloned instance with suspension state adjusted
     */
    public function withoutSuspension(): static
    {
        return tap(clone $this, fn(self $clone) => $clone->suspended = true);
    }

    /**
     * Determine if the given tokenable entity or its platform-specific token
     * is currently suspended.
     *
     * This method checks the blacklist repository using progressively less
     * specific keys (e.g., `User:123:api`, `User:123`) to determine if a
     * suspension flag exists for the tokenable or its platform.
     *
     * @param Tokenable $tokenable The tokenable model instance to check.
     * @param string    $platform  The platform or context to check (defaults to 'default').
     *
     * @return bool True if the token or tokenable is suspended, false otherwise.
     */
    protected function isSuspended(Tokenable $tokenable, string $platform = 'default'): bool
    {
        $disabled = config('tokenable.suspend_enabled', true) === false;

        if ($disabled || $this->suspended) {
            return false;
        }

        $keys = [
            get_class($tokenable),
            $tokenable->getKey(),
            $platform,
        ];

        for ($i = 1; $i < count($keys); $i++) {
            $key = implode('.', array_slice($keys, 0, $i + 1));
            try {
                if ($this->repository->has($key)) {
                    return true;
                }
            } catch (\Throwable $e) {
                //
            }
        }

        return false;
    }

    /**
     * Refresh the access and refresh tokens using the current refresh token.
     *
     * @param Request $request
     *
     * @return Token|null
     */
    public function refreshToken(Request $request): ?Token
    {
        $token = $this->getRefreshTokenFromRequest($request);

        if (is_null($token)) {
            return null;
        }

        $token = $this->tokenManager->driver(
            $this->tokenManager->normalizeDriverName($request->getUser())
        )->setRefreshToken($token);

        try {
            if ($this->blacklist->isBlacklistEnabled() && $this->blacklist->has($token->getRefreshToken())) {
                return null;
            }
        } catch (InvalidArgumentException $e) {
            return null;
        }

        $authentication = $this->authentication->findRefreshToken($token->getRefreshToken());

        if (is_null($authentication) ||
            !$this->isValidAuthenticationToken($authentication, $tokenable = $authentication->getRelation('tokenable')) ||
            !$this->supportsTokens($tokenable)) {
            return null;
        }

        $accessExpireAt     = config('tokenable.ttl', 7200);
        $refreshAvailableAt = config('tokenable.refresh_nbf', 3600);
        $refreshExpireAt    = config('tokenable.refresh_ttl', 'P15D');
        $authentication     = $authentication->fill([
            'access_token_expire_at'     => $this->getDateTimeAt($accessExpireAt),
            'refresh_token_available_at' => $this->getDateTimeAt($refreshAvailableAt),
            'refresh_token_expire_at'    => $this->getDateTimeAt($refreshExpireAt),
        ]);
        $token              = $this->getToken()->buildTokens($authentication, $tokenable);

        $this->setAuthentication($authentication)->setTokenable($tokenable)->setToken($token);

        return tap($token, function (Token $token) use ($authentication, $tokenable) {
            $attributes = $authentication->getAttributes();
            if ($authentication->fill([
                'token_driver'  => $token->getName(),
                'access_token'  => $token->getAccessToken(),
                'refresh_token' => $token->getRefreshToken(),
            ])->save()) {
                event(new AccessTokenRevoked($attributes));
                event(new AccessTokenRefreshed($authentication));
            }
        });
    }

    /**
     * Revoke (invalidate) the both access and refresh tokens by access token.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function revokeToken(Request $request): bool
    {
        if (is_null($token = $this->getAccessTokenFromRequest($request))) {
            return false;
        }

        $token = $this->getTokenManager()->driver(
            $this->getTokenManager()->normalizeDriverName($request->getUser())
        )->setAccessToken($token);

        $authentication = $this->getAuthentication()->findAccessToken($token->getAccessToken());

        if (is_null($authentication)) {
            return false;
        }

        if ($this->getAuthentication()->delete()) {
            event(new AccessTokenRevoked($this->getAuthentication()->getAttributes()));
            return true;
        }

        return false;
    }
}
