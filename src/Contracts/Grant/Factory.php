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
     * Set the AccessTokenGrant instance.
     *
     * @param AccessTokenGrant $accessTokenGrant
     *
     * @return static
     */
    public function setAccessTokenGrant(AccessTokenGrant $accessTokenGrant): static;

    /**
     * Get the AuthorizationCodeGrant instance.
     *
     * @return AuthorizationCodeGrant
     */
    public function getAuthorizationCodeGrant(): AuthorizationCodeGrant;

    /**
     * Set the AuthorizationCodeGrant instance.
     *
     * @param AuthorizationCodeGrant $authorizationCodeGrant
     *
     * @return $this
     */
    public function setAuthorizationCodeGrant(AuthorizationCodeGrant $authorizationCodeGrant): static;

    /**
     * Get the RefreshTokenGrant instance.
     *
     * @return RefreshTokenGrant
     */
    public function getRefreshTokenGrant(): RefreshTokenGrant;

    /**
     * Set the RefreshTokenGrant instance.
     *
     * @param RefreshTokenGrant $refreshTokenGrant
     *
     * @return static
     */
    public function setRefreshTokenGrant(RefreshTokenGrant $refreshTokenGrant): static;

    /**
     * Get the RevokeTokenGrant instance.
     *
     * @return RevokeTokenGrant
     */
    public function getRevokeTokenGrant(): RevokeTokenGrant;

    /**
     * Set the RevokeTokenGrant instance.
     *
     * @param RevokeTokenGrant $revokeTokenGrant
     *
     * @return static
     */
    public function setRevokeTokenGrant(RevokeTokenGrant $revokeTokenGrant): static;
}
