<?php

namespace Jundayw\Tokenable\Concerns\Grant;

use Illuminate\Http\Request;
use Jundayw\Tokenable\Tokenable;

trait AuthorizationCodeHelper
{
    /**
     * Get the auth code for the current request.
     *
     * @param Request $request
     *
     * @return string|null
     */
    protected function getAuthCodeFromRequest(Request $request): ?string
    {
        if (is_callable($tokenRetrievalCallback = Tokenable::tokenRetrievalCallback())) {
            return call_user_func($tokenRetrievalCallback, $request, $this);
        }

        $token = $request->bearerToken();

        if (empty($token)) {
            $token = $request->getPassword();
        }

        if (empty($token)) {
            $token = $this->getAuthCodeViaCookie($request);
        }

        return $this->isValidToken($token) ? $token : null;
    }

    /**
     * Get the auth code cookie via the incoming request.
     *
     * @param Request $request
     *
     * @return string|null
     */
    protected function getAuthCodeViaCookie(Request $request): ?string
    {
        $token = $request->cookie('auth_code');

        if (is_callable($accessTokenViaCookieCallback = Tokenable::authCodeViaCookieCallback())) {
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
}
