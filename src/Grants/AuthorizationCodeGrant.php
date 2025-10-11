<?php

namespace Jundayw\Tokenable\Grants;

use Illuminate\Http\Request;
use Jundayw\Tokenable\Concerns\Grant\AuthorizationCodeHelper;
use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant as AuthorizationCodeGrantContract;
use Jundayw\Tokenable\Contracts\Token\Token;

class AuthorizationCodeGrant extends Grant implements AuthorizationCodeGrantContract
{
    use AuthorizationCodeHelper;

    /**
     * Log the given auth code into the application.
     *
     * @param Request $request
     *
     * @return static|null
     */
    public function fromAuthCode(Request $request): ?static
    {
        if (is_null($authCode = $this->getAuthCodeFromRequest($request))) {
            return null;
        }

        try {
            $authCode  = $this->getTokenManager()->driver(
                $this->getTokenManager()->normalizeDriverName($request->getUser())
            )->setAuthorizationCode($authCode);
            $authCode  = $authCode->getAuthorizationCode();
            $tokenable = $this->repository->get("auth_code_{$authCode}");

            if (!is_null($tokenable)) {
                return $this->setTokenable($tokenable);
            }
        } catch (\Throwable $e) {
            //
        }

        return null;
    }

    /**
     * Create a new access token for the user.
     *
     * @param string $name
     * @param string $platform
     * @param array  $scopes
     *
     * @return Token|null
     */
    public function createToken(string $name, string $platform = 'default', array $scopes = []): ?Token
    {
        return $this->getGuard()
            ->login($this->getTokenable())
            ->setToken($this->getToken())
            ->withoutSuspension()
            ->createToken($name, $platform, $scopes);
    }

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
    public function createAuthCode(): ?Token
    {
        if (is_null($tokenable = $this->getTokenable())) {
            return null;
        }

        $token = $this->getToken()->buildAuthCode($tokenable->tokens()->getModel(), $tokenable);

        $this->repository->put(
            "auth_code_{$token->getAuthorizationCode()}",
            $tokenable,
            now()->addSeconds(config('tokenizer.auth_code_ttl', 60))
        );

        return $token;
    }
}
