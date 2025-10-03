<?php

namespace Jundayw\Tokenable\Grants;

use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant as AuthorizationCodeGrantContract;
use Jundayw\Tokenable\Contracts\Token\Token;

class AuthorizationCodeGrant extends Grant implements AuthorizationCodeGrantContract
{
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

        return tap(
            $this->getToken()->buildAuthCode($tokenable->tokens()->getModel(), $tokenable),
            fn(Token $token) => $this->repository->put(
                "auth_code_{$token->getAuthorizationCode()}",
                $tokenable,
                now()->addSeconds(config('tokenizer.auth_code_ttl', 60))
            )
        );
    }
}
