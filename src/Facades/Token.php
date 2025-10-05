<?php

namespace Jundayw\Tokenable\Facades;

use Illuminate\Support\Facades\Facade;
use Jundayw\Tokenable\Contracts\Grant\Factory;
use Jundayw\Tokenable\Grants\AccessTokenGrant;
use Jundayw\Tokenable\Grants\AuthorizationCodeGrant;

/**
 * @method static AccessTokenGrant getAccessTokenGrant()
 * @method static AuthorizationCodeGrant getAuthorizationCodeGrant()
 *
 * @see GrantManager
 * @see AccessTokenGrant
 * @see AuthorizationCodeGrant
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
