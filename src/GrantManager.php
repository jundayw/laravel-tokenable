<?php

namespace Jundayw\Tokenable;

use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant;
use Jundayw\Tokenable\Contracts\Grant\RefreshTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\RevokeTokenGrant;

class GrantManager implements Contracts\Grant\Factory
{
    public function __construct(
        protected AccessTokenGrant $accessTokenGrant,
        protected AuthorizationCodeGrant $authorizationCodeGrant,
        protected RefreshTokenGrant $refreshTokenGrant,
        protected RevokeTokenGrant $revokeTokenGrant,
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
     * Set the AccessTokenGrant instance.
     *
     * @param AccessTokenGrant $accessTokenGrant
     *
     * @return static
     */
    public function setAccessTokenGrant(AccessTokenGrant $accessTokenGrant): static
    {
        $this->accessTokenGrant = $accessTokenGrant;
        return $this;
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

    /**
     * Set the AuthorizationCodeGrant instance.
     *
     * @param AuthorizationCodeGrant $authorizationCodeGrant
     *
     * @return $this
     */
    public function setAuthorizationCodeGrant(AuthorizationCodeGrant $authorizationCodeGrant): static
    {
        $this->authorizationCodeGrant = $authorizationCodeGrant;
        return $this;
    }

    /**
     * Get the RefreshTokenGrant instance.
     *
     * @return RefreshTokenGrant
     */
    public function getRefreshTokenGrant(): RefreshTokenGrant
    {
        return $this->refreshTokenGrant;
    }

    /**
     * Set the RefreshTokenGrant instance.
     *
     * @param RefreshTokenGrant $refreshTokenGrant
     *
     * @return static
     */
    public function setRefreshTokenGrant(RefreshTokenGrant $refreshTokenGrant): static
    {
        $this->refreshTokenGrant = $refreshTokenGrant;
        return $this;
    }

    /**
     * Get the RevokeTokenGrant instance.
     *
     * @return RevokeTokenGrant
     */
    public function getRevokeTokenGrant(): RevokeTokenGrant
    {
        return $this->revokeTokenGrant;
    }

    /**
     * Set the RevokeTokenGrant instance.
     *
     * @param RevokeTokenGrant $revokeTokenGrant
     *
     * @return static
     */
    public function setRevokeTokenGrant(RevokeTokenGrant $revokeTokenGrant): static
    {
        $this->revokeTokenGrant = $revokeTokenGrant;
        return $this;
    }
}
