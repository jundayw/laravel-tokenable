<?php

namespace Jundayw\Tokenable\Facades;

use Illuminate\Support\Facades\Facade;
use Jundayw\Tokenable\Contracts\Grant\AccessTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\AuthorizationCodeGrant;
use Jundayw\Tokenable\Contracts\Grant\Factory;
use Jundayw\Tokenable\Contracts\Grant\RefreshTokenGrant;
use Jundayw\Tokenable\Contracts\Grant\RevokeTokenGrant;

/**
 * @method static AccessTokenGrant getAccessTokenGrant()
 * @method static AuthorizationCodeGrant getAuthorizationCodeGrant()
 * @method static RefreshTokenGrant getRefreshTokenGrant()
 * @method static RevokeTokenGrant getRevokeTokenGrant()
 *
 * @see GrantManager
 */
class Token extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}
