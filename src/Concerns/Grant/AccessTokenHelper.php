<?php

namespace Jundayw\Tokenable\Concerns\Grant;

use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Jundayw\Tokenable\Contracts\Auth\Authenticable;
use Jundayw\Tokenable\Contracts\Auth\TokenableAuthGuard;
use Jundayw\Tokenable\Contracts\Tokenable as TokenableContract;
use Jundayw\Tokenable\HasTokenable;
use Jundayw\Tokenable\Tokenable;

trait AccessTokenHelper
{
    /**
     * Get the access token for the current request.
     *
     * @param Request $request
     *
     * @return string|null
     */
    protected function getAccessTokenFromRequest(Request $request): ?string
    {
        if (is_callable($tokenRetrievalCallback = Tokenable::tokenRetrievalCallback())) {
            return call_user_func($tokenRetrievalCallback, $request, $this);
        }

        $token = $request->bearerToken();

        if (empty($token)) {
            $token = $request->getPassword();
        }

        if (empty($token)) {
            $token = $this->getAccessTokenViaCookie($request);
        }

        return $this->isValidToken($token) ? $token : null;
    }

    /**
     * Get the refresh token for the current request.
     *
     * @param Request $request
     *
     * @return string|null
     */
    protected function getRefreshTokenFromRequest(Request $request): ?string
    {
        if (is_callable($tokenRetrievalCallback = Tokenable::tokenRetrievalCallback())) {
            return call_user_func($tokenRetrievalCallback, $request, $this);
        }

        $token = $request->bearerToken();

        if (empty($token)) {
            $token = $request->getPassword();
        }

        if (empty($token)) {
            $token = $this->getRefreshTokenViaCookie($request);
        }

        return $this->isValidToken($token) ? $token : null;
    }

    /**
     * Get the access token cookie via the incoming request.
     *
     * @param Request $request
     *
     * @return string|null
     */
    protected function getAccessTokenViaCookie(Request $request): ?string
    {
        $token = $request->cookie('access_token');

        if (is_callable($accessTokenViaCookieCallback = Tokenable::accessTokenViaCookieCallback())) {
            return call_user_func($accessTokenViaCookieCallback, $token, $request, $this);
        }

        return $token ?? null;
    }

    /**
     * Get the refresh token cookie via the incoming request.
     *
     * @param Request $request
     *
     * @return string|null
     */
    protected function getRefreshTokenViaCookie(Request $request): ?string
    {
        $token = $request->cookie('refresh_token');

        if (is_callable($accessTokenViaCookieCallback = Tokenable::refreshTokenViaCookieCallback())) {
            return call_user_func($accessTokenViaCookieCallback, $token, $request, $this);
        }

        return $token ?? null;
    }

    /**
     * Determine if the token is in the correct format.
     *
     * @param string|null $token
     *
     * @return bool
     */
    protected function isValidToken(string $token = null): bool
    {
        if (is_null($token)) {
            return false;
        }

        if (is_callable($tokenVerificationCallback = Tokenable::tokenVerificationCallback())) {
            return call_user_func($tokenVerificationCallback, $token);
        }

        return true;
    }

    /**
     * Determine if the provided token is valid.
     *
     * @param Authenticable|null     $authentication
     * @param TokenableContract|null $tokenable
     *
     * @return bool
     */
    protected function isValidAuthenticationToken(Authenticable $authentication = null, TokenableContract $tokenable = null): bool
    {
        if (is_null($authentication) || is_null($tokenable)) {
            return false;
        }

        $isValid = $this->hasValidProvider($tokenable);

        if (is_callable($tokenAuthenticationCallback = Tokenable::tokenAuthenticationCallback())) {
            $isValid = call_user_func($tokenAuthenticationCallback, $authentication, $tokenable, $isValid);
        }

        return $isValid;
    }

    /**
     * Determine if the tokenable model matches the provider's model type.
     *
     * @param TokenableContract $tokenable
     *
     * @return bool
     */
    protected function hasValidProvider(TokenableContract $tokenable): bool
    {
        if (!$this->getGuard() instanceof TokenableAuthGuard) {
            return false;
        }

        if (is_null($provider = $this->getGuard()->getConfig()->get('provider') ?? null)) {
            return true;
        }

        $model = config("auth.providers.{$provider}.model");

        return $tokenable instanceof $model;
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param TokenableContract $tokenable
     *
     * @return bool
     */
    protected function supportsTokens(TokenableContract $tokenable): bool
    {
        return in_array(HasTokenable::class, class_uses_recursive(
            get_class($tokenable)
        ));
    }

    /**
     * Return an array of scopes associated with the token.
     *
     * @return string[]
     */
    protected function getScopes(array $scopes): array
    {
        if (in_array('*', $scopes) || in_array('*', $this->getTokenable()->getScopes())) {
            return ['*'];
        }

        return array_merge($scopes, $this->getTokenable()->getScopes());
    }

    /**
     * Get a DateTime object after a specified duration.
     *
     * @param string|int $duration ISO 8601 duration string or integer number of seconds
     * @param int        $default  Default seconds to use if string parsing fails, default is 7200 (2 hours)
     *
     * @return DateTime The calculated DateTime object
     */
    protected function getDateTimeAt(string|int $duration = 0, int $default = 7200): DateTime
    {
        if (is_string($duration)) {
            try {
                return now()->add(new DateInterval($duration))->toDateTime();
            } catch (Exception $e) {
                return now()->addSeconds($default)->toDateTime();
            }
        }

        return now()->addSeconds($duration)->toDateTime();
    }
}
