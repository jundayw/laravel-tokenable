<?php

namespace Jundayw\Tokenable;

use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant;

class GrantManager implements Contracts\Grant\Factory
{
    public function __construct(
        protected AccessTokenGrant $accessTokenGrant,
        protected AuthorizationCodeGrant $authorizationCodeGrant,
    ) {
        //
    }

    /**
     * Get the AccessTokenGrant instance.
     *
     * @return AccessTokenGrant
     */
    public function getAccessTokenGrant(): AccessTokenGrant
    {
        return $this->accessTokenGrant;
    }

    /**
     * Get the AuthorizationCodeGrant instance.
     *
     * @return AuthorizationCodeGrant
     */
    public function getAuthorizationCodeGrant(): AuthorizationCodeGrant
    {
        return $this->authorizationCodeGrant;
    }
}
