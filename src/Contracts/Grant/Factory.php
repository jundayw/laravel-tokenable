<?php

namespace Jundayw\Tokenable\Contracts\Grant;

interface Factory
{
    /**
     * Get the AccessTokenGrant instance.
     *
     * @return AccessTokenGrant
     */
    public function getAccessTokenGrant(): AccessTokenGrant;

    /**
     * Get the AuthorizationCodeGrant instance.
     *
     * @return AuthorizationCodeGrant
     */
    public function getAuthorizationCodeGrant(): AuthorizationCodeGrant;
}
