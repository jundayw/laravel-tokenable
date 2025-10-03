<?php

namespace Jundayw\Tokenable\Grants;

use Illuminate\Http\Request;
use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant as AccessTokenGrantContract;
use Jundayw\Tokenable\Contracts\Token\Token;
use Jundayw\Tokenable\Contracts\Tokenable as TokenableContract;
use Jundayw\Tokenable\Events\AccessTokenCreated;
use Jundayw\Tokenable\Events\AccessTokenRefreshed;
use Jundayw\Tokenable\Events\AccessTokenRefreshing;
use Jundayw\Tokenable\Events\AccessTokenRevoked;

class AccessTokenGrant extends Grant implements AccessTokenGrantContract
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

        $authentication = $this->getAuthentication()->findAccessToken($token->getAccessToken());

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
                event(new AccessTokenCreated($this->resolveGuard()->getConfig(), $authentication, $tokenable, $token));
            }
        });
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
            [
                $originalAuthentication, $originalTokenable, $originalToken,
            ] = array_map(fn($instance) => clone $instance, [$authentication, $tokenable, $token]);
            if ($authentication->fill([
                'token_driver'  => $token->getName(),
                'access_token'  => $token->getAccessToken(),
                'refresh_token' => $token->getRefreshToken(),
            ])->save()) {
                event(new AccessTokenRefreshing($originalAuthentication, $originalTokenable, $originalToken));
                event(new AccessTokenRefreshed($authentication, $tokenable, $token));
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
        if (is_null($this->findToken($request))) {
            return false;
        }

        if (!$this->getAuthentication()->exists) {
            return false;
        }

        if ($this->getAuthentication()->delete()) {
            event(new AccessTokenRevoked($this->getAuthentication(), $this->getTokenable(), $this->getToken()));
            return true;
        }

        return false;
    }
}
